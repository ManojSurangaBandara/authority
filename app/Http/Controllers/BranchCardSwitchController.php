<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusPassApplication;
use App\Models\BranchCardSwitchHistory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BranchCardSwitchController extends Controller
{
    /**
     * Display the switch to branch card form.
     */
    public function index()
    {
        return view('branch-card-switch.index');
    }

    /**
     * Handle the switch to branch card request.
     */
    public function switch(Request $request)
    {
        $request->validate([
            'regiment_no' => 'required|string',
            'branch_card_id' => 'required|string',
        ]);

        try {
            // Find application by regiment number
            $application = BusPassApplication::whereHas('person', function ($query) use ($request) {
                $query->where('regiment_no', $request->regiment_no);
            })->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'No application found for the given regiment number.'
                ], 404);
            }

            // Look for applications that can be switched
            // $application = BusPassApplication::whereHas('person', function ($query) use ($request) {
            //     $query->where('regiment_no', $request->regiment_no);
            // })
            //     ->where('status', 'integrated_to_temp_card')
            //     ->where('branch_card_availability', 'no_branch_card')
            //     ->first();

            // Check if branch user can only access applications from their establishment
            $user = Auth::user();
            if ($user->isBranchUser()) {
                if ($application->establishment_id !== $user->establishment_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You can only switch applications from your own establishment.'
                    ], 403);
                }
            }

            if ($application->status === "integrated_to_branch_card") {
                return response()->json([
                    'success' => false,
                    'message' => 'Already Integrated to Branch Card.'
                ], 404);
            }

            if (!$application->status === "integrated_to_temp_card") {
                return response()->json([
                    'success' => false,
                    'message' => 'No temporary card found for the given regiment number.'
                ], 404);
            }

            // Verify branch card
            $verificationResponse = $this->verifyBranchCard($request->regiment_no, $request->branch_card_id);

            if (!$verificationResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Branch card verification failed: ' . $verificationResponse['message']
                ], 400);
            }

            // Update application
            $oldBranchCardId = $application->branch_card_id;
            $oldTempCardQr = $application->temp_card_qr;
            $application->update([
                'branch_card_availability' => 'has_branch_card',
                'branch_card_id' => $request->branch_card_id,
                'temp_card_qr' => null,
                'status' => 'integrated_to_branch_card',
            ]);

            // Record successful switch
            BranchCardSwitchHistory::create([
                'bus_pass_application_id' => $application->id,
                'regiment_no' => $request->regiment_no,
                'old_branch_card_id' => $oldBranchCardId,
                'old_temp_card_qr' => $oldTempCardQr,
                'new_branch_card_id' => $request->branch_card_id,
                'action' => 'switched_from_temp_card_to_branch_card',
                'remarks' => 'Successfully switched to branch card',
                'performed_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully switched to branch card.'
            ]);
        } catch (\Exception $e) {
            Log::error('Branch card switch error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request.'
            ], 500);
        }
    }

    /**
     * Verify branch card with external API.
     */
    private function verifyBranchCard($serviceNo, $serNo)
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->get('https://branchcard.army.lk/bcims_api/person.php', [
                    'service_no' => $serviceNo,
                    'ser_no' => $serNo
                ]);

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['person']) && $responseData['person'] !== null && count($responseData['person']) > 0) {
                    return [
                        'success' => true,
                        'person' => $responseData['person']
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Invalid branch card details'
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Branch card verification service unavailable'
            ];
        } catch (\Exception $e) {
            Log::error('Branch card verification error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Branch card verification failed: ' . $e->getMessage()
            ];
        }
    }
}
