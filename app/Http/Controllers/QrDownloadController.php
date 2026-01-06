<?php

namespace App\Http\Controllers;

use App\DataTables\QrDownloadDataTable;
use App\Models\Establishment;
use Illuminate\Support\Facades\Auth;
use App\Models\BusPassApplication;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrDownloadController extends Controller
{
    /**
     * Display the QR Download page
     */
    public function index(QrDownloadDataTable $dataTable)
    {
        // Filter establishments for branch users, but not for DMOV users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];
        $dmovRoles = [
            'System Administrator (DMOV)',
            'Subject Clerk (DMOV)',
            'Staff Officer 2 (DMOV)',
            'Staff Officer 1 (DMOV)',
            'Col Mov (DMOV)',
            'Director (DMOV)',
            'Bus Escort (DMOV)'
        ];

        if ($user && $user->hasAnyRole($branchRoles) && $user->establishment_id && !$user->hasAnyRole($dmovRoles)) {
            $establishments = Establishment::where('id', $user->establishment_id)->get();
        } else {
            $establishments = Establishment::all();
        }

        return $dataTable->render('qr-download.index', compact('establishments'));
    }

    /**
     * Download QR code for a specific application
     *
     * This method combines a QR code with a template image (qr_format.jpeg)
     * Requirements:
     * - Place a qr_format.jpeg file in the public/ folder
     * - The template should be designed to accommodate a QR code overlay
     * - QR code will be centered by default, but can be positioned specifically
     */
    public function download($id)
    {
        $application = BusPassApplication::findOrFail($id);

        // Check if application has temp card QR
        if (!$application->temp_card_qr) {
            abort(404, 'QR code not found for this application');
        }

        // Path to the QR format template
        $templatePath = public_path('storage/qr_format.jpeg');

        // Check if template exists
        if (!file_exists($templatePath)) {
            // If template doesn't exist, return just the QR code
            return $this->generatePlainQrCode($application);
        }

        // Load the template image
        $templateImage = imagecreatefromjpeg($templatePath);
        if (!$templateImage) {
            // If template can't be loaded, return just the QR code
            return $this->generatePlainQrCode($application);
        }

        // Get template dimensions
        $templateWidth = imagesx($templateImage);
        $templateHeight = imagesy($templateImage);

        // Generate QR code as image resource
        $options = new QROptions([
            'version' => 5,
            'outputType' => QRCode::OUTPUT_IMAGE_JPG,
            'eccLevel' => QRCode::ECC_L,
            'imageBase64' => false,
            'imageTransparent' => false,
            'scale' => 6, // Smaller scale for overlay
            'imageWidth' => 200, // Smaller size for overlay
            'imageHeight' => 200,
        ]);

        $qrcode = new QRCode($options);
        $qrCodeData = $qrcode->render($application->temp_card_qr);

        // Create QR code image from the binary data
        $qrImage = imagecreatefromstring($qrCodeData);
        if (!$qrImage) {
            // If QR code can't be created, return just the template
            return $this->returnImage($templateImage, $application);
        }

        // Get QR code dimensions
        $qrWidth = imagesx($qrImage);
        $qrHeight = imagesy($qrImage);

        // Calculate position to center the QR code on the template
        // You may need to adjust these coordinates based on your template design
        $qrX = ($templateWidth - $qrWidth) / 5; // Center horizontally
        $qrY = ($templateHeight - $qrHeight) / 2; // Center vertically

        // For better positioning, you might want to position it at specific coordinates
        // Uncomment and adjust the lines below if you need specific positioning
        // $qrX = 100; // Specific X coordinate from left
        // $qrY = 150; // Specific Y coordinate from top

        // Copy QR code onto template
        imagecopy($templateImage, $qrImage, $qrX, $qrY, 0, 0, $qrWidth, $qrHeight);

        // Add route information text at the bottom
        $this->addRouteTextToImage($templateImage, $application);

        // Clean up QR image resource
        imagedestroy($qrImage);

        // Return the combined image
        return $this->returnImage($templateImage, $application);
    }

    /**
     * Generate and return plain QR code (fallback method)
     */
    private function generatePlainQrCode($application)
    {
        $options = new QROptions([
            'version' => 5,
            'outputType' => QRCode::OUTPUT_IMAGE_JPG,
            'eccLevel' => QRCode::ECC_L,
            'imageBase64' => false,
            'imageTransparent' => false,
            'scale' => 10,
            'imageWidth' => 300,
            'imageHeight' => 300,
        ]);

        $qrcode = new QRCode($options);
        $qrCode = $qrcode->render($application->temp_card_qr);

        $filename = 'QR_' . $application->person->regiment_no . '_' . $application->id . '.jpg';

        return response($qrCode)
            ->header('Content-Type', 'image/jpeg')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Return image as downloadable file
     */
    private function returnImage($image, $application)
    {
        // Start output buffering
        ob_start();

        // Output the image
        imagejpeg($image, null, 90); // 90% quality

        // Get the image data
        $imageData = ob_get_clean();

        // Clean up image resource
        imagedestroy($image);

        // Create filename
        $filename = 'QR_Card_' . $application->person->regiment_no . '_' . $application->id . '.jpg';

        // Return the image as downloadable file
        return response($imageData)
            ->header('Content-Type', 'image/jpeg')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Add route information text to the image
     */
    private function addRouteTextToImage($image, $application)
    {
        // Get route information based on pass type
        $routeText = $this->getRouteText($application);

        if (empty($routeText)) {
            return; // No route information to display
        }

        // Set text color (black)
        $textColor = imagecolorallocate($image, 0, 0, 0);

        // Try to use a better font if available, otherwise use GD built-in
        $fontSize = 7; // GD built-in font size (1-5)
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        // Position text at the bottom center with some margin
        $textY = $imageHeight - 50; // 25 pixels from bottom

        // Calculate text width for centering
        $textWidth = imagefontwidth($fontSize) * strlen($routeText);
        $textX = max(10, ($imageWidth - $textWidth) / 2); // Ensure minimum left margin

        // Add a white background rectangle for better text readability
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        $padding = 4;
        imagefilledrectangle(
            $image,
            $textX - $padding,
            $textY - $padding,
            $textX + $textWidth + $padding,
            $textY + imagefontheight($fontSize) + $padding,
            $bgColor
        );

        // Add black border around text background
        $borderColor = imagecolorallocate($image, 0, 0, 0);
        imagerectangle(
            $image,
            $textX - $padding,
            $textY - $padding,
            $textX + $textWidth + $padding,
            $textY + imagefontheight($fontSize) + $padding,
            $borderColor
        );

        // Add the text to the image
        imagestring($image, $fontSize, $textX, $textY, $routeText, $textColor);
    }

    /**
     * Get route text based on application type
     */
    private function getRouteText($application)
    {
        $routeInfo = [];

        switch ($application->bus_pass_type) {
            case 'daily_travel':
            case 'unmarried_daily_travel':
                if ($application->requested_bus_name) {
                    $routeInfo[] = $application->requested_bus_name;
                }
                if ($application->daily_route_from && $application->daily_route_to) {
                    $routeInfo[] = $application->daily_route_from . ' - ' . $application->daily_route_to;
                }
                break;

            case 'weekend_monthly_travel':
                if ($application->weekend_bus_name) {
                    $routeInfo[] = $application->weekend_bus_name;
                }
                if ($application->living_in_bus) {
                    $routeInfo[] = $application->living_in_bus;
                }
                if ($application->weekend_route_from && $application->weekend_route_to) {
                    $routeInfo[] = $application->weekend_route_from . ' - ' . $application->weekend_route_to;
                }
                break;

            case 'living_in_only':
                if ($application->living_in_bus) {
                    $routeInfo[] = $application->living_in_bus;
                }
                break;

            case 'weekend_only':
                if ($application->weekend_bus_name) {
                    $routeInfo[] = $application->weekend_bus_name;
                }
                if ($application->weekend_route_from && $application->weekend_route_to) {
                    $routeInfo[] = $application->weekend_route_from . ' - ' . $application->weekend_route_to;
                }
                break;
        }

        // Remove empty values and join with commas
        $routeInfo = array_filter($routeInfo);
        return implode(', ', $routeInfo);
    }
}
