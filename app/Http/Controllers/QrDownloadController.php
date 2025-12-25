<?php

namespace App\Http\Controllers;

use App\DataTables\QrDownloadDataTable;
use App\Models\Establishment;
use Illuminate\Support\Facades\Auth;

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
     */
    public function download($id)
    {
        $application = BusPassApplication::findOrFail($id);

        // Check if application has temp card QR
        if (!$application->temp_card_qr) {
            abort(404, 'QR code not found for this application');
        }

        // Generate QR code as JPG using GD
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

        // Create filename
        $filename = 'QR_' . $application->person->regiment_no . '_' . $application->id . '.jpg';

        // Return the QR code as downloadable file
        return response($qrCode)
            ->header('Content-Type', 'image/jpeg')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
