<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Escort;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class EscortAuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Escort mobile app login
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'e_no' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $e_no = $request->e_no;
        $password = $request->password;

        try {
            // Step 1: Find escort by eno
            $escort = Escort::where('eno', $e_no)->first();

            if (!$escort) {
                Log::warning('Escort login failed - Escort not found', [
                    'e_no' => $e_no,
                ]);

                return $this->errorResponse('Escort not found', 404);
            }

            // Step 2: Authenticate with ePortal
            if (!$this->authenticateWithEPortal($e_no, $password)) {
                Log::warning('Escort login failed - Wrong ePortal credentials', [
                    'e_no' => $e_no,
                    'escort_id' => $escort->id,
                ]);

                return $this->unauthorizedResponse('Wrong credentials');
            }

            // Step 3: Generate JWT token for escort
            $customClaims = [
                'escort_id' => $escort->id,
                'regiment_no' => $escort->regiment_no,
                'eno' => $escort->eno,
                'name' => $escort->name,
                'rank' => $escort->rank,
                'type' => 'escort', // Distinguish from regular users
            ];

            $token = JWTAuth::claims($customClaims)->fromSubject($escort);

            Log::info('Escort login successful', [
                'e_no' => $e_no,
                'escort_id' => $escort->id,
                'escort_name' => $escort->name,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'escort' => [
                    'id' => $escort->id,
                    'regiment_no' => $escort->regiment_no,
                    'eno' => $escort->eno,
                    'name' => $escort->name,
                    'rank' => $escort->rank,
                    'contact_no' => $escort->contact_no,
                ],
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60
                ]
            ]);
        } catch (JWTException $e) {
            Log::error('Escort login failed - JWT error', [
                'e_no' => $e_no,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Could not create token', 500);
        } catch (\Exception $e) {
            Log::error('Escort login failed - Unexpected error', [
                'e_no' => $e_no,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Login failed', 500);
        }
    }

    /**
     * Get authenticated escort profile
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            // Verify this is an escort token
            if ($payload->get('type') !== 'escort') {
                return $this->forbiddenResponse('Invalid token type');
            }

            $escortId = $payload->get('escort_id');
            $escort = Escort::with(['escortAssignment'])->find($escortId);

            if (!$escort) {
                return $this->notFoundResponse('Escort not found');
            }

            return $this->successResponse([
                'escort' => [
                    'id' => $escort->id,
                    'regiment_no' => $escort->regiment_no,
                    'eno' => $escort->eno,
                    'name' => $escort->name,
                    'rank' => $escort->rank,
                    'contact_no' => $escort->contact_no,
                    'assignment' => $escort->escortAssignment ? [
                        'id' => $escort->escortAssignment->id,
                        'status' => $escort->escortAssignment->status,
                        'assigned_date' => $escort->escortAssignment->assigned_date,
                    ] : null,
                ]
            ], 'Escort profile retrieved successfully');
        } catch (JWTException $e) {
            return $this->unauthorizedResponse('Token invalid or expired');
        } catch (\Exception $e) {
            Log::error('Escort profile fetch failed', [
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Failed to get profile', 500);
        }
    }

    /**
     * Logout escort (invalidate token)
     */
    public function logout(): JsonResponse
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            // Verify this is an escort token
            if ($payload->get('type') !== 'escort') {
                return $this->forbiddenResponse('Invalid token type');
            }

            JWTAuth::invalidate($token);

            Log::info('Escort logout successful', [
                'escort_id' => $payload->get('escort_id'),
                'eno' => $payload->get('eno'),
                'regiment_no' => $payload->get('regiment_no'),
            ]);

            return $this->successResponse(null, 'Successfully logged out');
        } catch (JWTException $e) {
            return $this->errorResponse('Failed to logout, please try again', 500);
        }
    }

    /**
     * Refresh escort token
     */
    public function refresh(): JsonResponse
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            // Verify this is an escort token
            if ($payload->get('type') !== 'escort') {
                return $this->forbiddenResponse('Invalid token type');
            }

            $newToken = JWTAuth::refresh($token);

            return response()->json([
                'status' => 'success',
                'authorization' => [
                    'token' => $newToken,
                    'type' => 'bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60
                ]
            ]);
        } catch (JWTException $e) {
            return $this->unauthorizedResponse('Token could not be refreshed');
        }
    }

    /**
     * Authenticate escort with ePortal
     */
    protected function authenticateWithEPortal(string $e_no, string $password): bool
    {
        try {
            $response = Http::withoutVerifying()->timeout(30)->post('https://192.168.100.41/eportal/api/busspass_login', [
                'username' => $e_no,
                'password' => $password
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['status']) && $responseData['status'] === 'success') {
                    Log::info('ePortal authentication successful for escort', [
                        'e_no' => $e_no,
                        'user_data' => $responseData['user'] ?? null,
                    ]);
                    return true;
                }

                Log::warning('ePortal authentication failed - Status not success', [
                    'e_no' => $e_no,
                    'response' => $responseData
                ]);
                return false;
            }

            Log::error('ePortal authentication HTTP error', [
                'e_no' => $e_no,
                'status_code' => $response->status(),
                'response' => $response->body()
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('ePortal authentication error', [
                'e_no' => $e_no,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
