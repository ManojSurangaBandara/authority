<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\BusPassApplication;
use App\Models\BusRoute;
use App\Models\Escort;
use App\Models\Onboarding;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
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

            // Step 3: Get escort's active assignment with route and bus details
            $assignment = $escort->escortAssignment()->with([
                'busRoute.assignedBus',
                'livingInBus.assignedBus'
            ])->first();

            // Step 4: Generate JWT token for escort
            $customClaims = [
                'escort_id' => $escort->id,
                'regiment_no' => $escort->regiment_no,
                'eno' => $escort->eno,
                'name' => $escort->name,
                'rank' => $escort->rank,
                'type' => 'escort', // Distinguish from regular users
            ];

            $token = JWTAuth::claims($customClaims)->fromSubject($escort);

            // Step 5: Prepare route information
            $route = null;
            if ($assignment) {
                if ($assignment->route_type === 'living_out' && $assignment->busRoute) {
                    $route = [
                        'id' => $assignment->busRoute->id,
                        'route_type' => 'living_out',
                        'route_name' => $assignment->busRoute->name,
                        'bus' => $assignment->busRoute->assignedBus ? [
                            'id' => $assignment->busRoute->assignedBus->id,
                            'name' => $assignment->busRoute->assignedBus->name,
                            'no' => $assignment->busRoute->assignedBus->no,
                        ] : null
                    ];
                } elseif ($assignment->route_type === 'living_in' && $assignment->livingInBus) {
                    $route = [
                        'id' => $assignment->livingInBus->id,
                        'route_type' => 'living_in',
                        'route_name' => $assignment->livingInBus->name,
                        'bus' => $assignment->livingInBus->assignedBus ? [
                            'id' => $assignment->livingInBus->assignedBus->id,
                            'name' => $assignment->livingInBus->assignedBus->name,
                            'no' => $assignment->livingInBus->assignedBus->no,
                        ] : null
                    ];
                }
            }

            Log::info('Escort login successful', [
                'e_no' => $e_no,
                'escort_id' => $escort->id,
                'escort_name' => $escort->name,
                'route_assigned' => $route !== null,
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
                ],
                'route' => $route
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
     * Refresh escort token.
     */
    public function refresh(): JsonResponse
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            // Set the new token so we can read its payload
            JWTAuth::setToken($newToken);
            $payload = JWTAuth::getPayload();

            // Verify this is an escort token
            if ($payload->get('type') !== 'escort') {
                return $this->forbiddenResponse('Invalid token type');
            }

            $escortId = $payload->get('escort_id');
            $escort = Escort::find($escortId);

            if (!$escort) {
                return $this->notFoundResponse('Escort not found');
            }

            $data = [
                'escort' => [
                    'id' => $escort->id,
                    'regiment_no' => $escort->regiment_no,
                    'eno' => $escort->eno,
                    'name' => $escort->name,
                    'rank' => $escort->rank,
                    'contact_no' => $escort->contact_no,
                ],
                'authorization' => [
                    'token' => $newToken,
                    'type' => 'bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60
                ]
            ];

            return $this->successResponse($data, 'Token refreshed successfully');
        } catch (JWTException $e) {
            return $this->unauthorizedResponse('Token could not be refreshed');
        }
    }

    /**
     * Authenticate escort credentials with ePortal API
     */
    protected function authenticateWithEPortal(string $e_no, string $password): bool
    {
        try {
            $response = Http::withoutVerifying()->timeout(14400)->post('https://192.168.100.41/eportal/api/busspass_login', [
                'username' => $e_no,
                'password' => $password
            ]);

            // Check if the API call was successful and the response has success status
            if ($response->successful()) {
                $responseData = $response->json();

                // Check if the API returned success status
                if (isset($responseData['status']) && $responseData['status'] === 'success') {
                    Log::info('ePortal Authentication successful for E No: ' . $e_no, [
                        'user_data' => $responseData['user'] ?? null,
                        'token_type' => $responseData['authorisation']['type'] ?? null
                    ]);
                    return true;
                }

                // Log the API response if status is not success
                Log::warning('ePortal Authentication failed - Status not success', [
                    'e_no' => $e_no,
                    'response' => $responseData
                ]);
                return false;
            }

            // Log HTTP error if API call failed
            Log::error('ePortal Authentication HTTP error', [
                'e_no' => $e_no,
                'status_code' => $response->status(),
                'response' => $response->body()
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('ePortal authentication error: ' . $e->getMessage(), [
                'e_no' => $e_no,
            ]);
            return false;
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
     * Validate boarding permission by scanning QR code
     */
    public function validateBoarding(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'serial_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            // Verify this is an escort token
            if ($payload->get('type') !== 'escort') {
                return $this->forbiddenResponse('Invalid token type');
            }

            $escortId = $payload->get('escort_id');
            $escort = Escort::with(['escortAssignment.busRoute', 'escortAssignment.livingInBus'])->find($escortId);

            if (!$escort) {
                return $this->notFoundResponse('Escort not found');
            }

            // Check if escort has active assignment
            if (!$escort->escortAssignment) {
                Log::warning('Boarding validation failed - No active assignment', [
                    'escort_id' => $escortId,
                    'serial_number' => $request->serial_number,
                ]);
                return $this->errorResponse('Escort has no active bus assignment', 403);
            }

            $assignment = $escort->escortAssignment;

            // Decrypt the serial number to get branch card ID
            $branchCardId = $this->decryptSerialNumber($request->serial_number);

            if (!$branchCardId) {
                Log::warning('Boarding validation failed - Invalid serial number', [
                    'escort_id' => $escortId,
                    'serial_number' => $request->serial_number,
                ]);
                return $this->errorResponse('Invalid QR code', 400);
            }

            // Check if branch card exists and is valid
            $busPassApplication = BusPassApplication::where('branch_card_id', $branchCardId)
                ->whereIn('status', ['integrated_to_branch_card', 'temp_card_handed_over'])
                ->first();

            if (!$busPassApplication) {
                Log::warning('Boarding validation failed - Branch card not found or invalid', [
                    'escort_id' => $escortId,
                    'branch_card_id' => $branchCardId,
                    'serial_number' => $request->serial_number,
                ]);
                return $this->successResponse([
                    'allowed' => false,
                    'reason' => 'Branch card not found or invalid'
                ], 'Boarding not allowed');
            }

            // Check if the branch card is allowed for the escort's assigned route
            $isAllowed = false;
            $routeName = '';
            $routeId = null;

            if ($assignment->route_type === 'living_out' && $assignment->busRoute) {
                $isAllowed = $this->isBranchCardAllowedForRoute($busPassApplication, $assignment->busRoute);
                $routeName = $assignment->busRoute->name;
                $routeId = $assignment->busRoute->id;
            } elseif ($assignment->route_type === 'living_in' && $assignment->livingInBus) {
                $isAllowed = $this->isBranchCardAllowedForLivingInRoute($busPassApplication, $assignment->livingInBus);
                $routeName = $assignment->livingInBus->name;
                $routeId = $assignment->livingInBus->id;
            }

            Log::info('Boarding validation completed', [
                'escort_id' => $escortId,
                'branch_card_id' => $branchCardId,
                'route_type' => $assignment->route_type,
                'route_id' => $routeId,
                'allowed' => $isAllowed,
            ]);

            // Generate full URL for person image
            $personImageUrl = null;
            if ($busPassApplication->person_image) {
                $personImageUrl = asset('storage/' . $busPassApplication->person_image);
            }

            return $this->successResponse([
                'allowed' => $isAllowed,
                'application_id' => $busPassApplication->id,
                'branch_card_id' => $branchCardId,
                'passenger_name' => $busPassApplication->person->name ?? 'Unknown',
                'passenger_image_url' => $personImageUrl,
                'bus_route' => $routeName,
                'reason' => $isAllowed ? null : 'Branch card not authorized for this route'
            ], $isAllowed ? 'Boarding allowed' : 'Boarding not allowed');
        } catch (JWTException $e) {
            return $this->unauthorizedResponse('Token invalid or expired');
        } catch (\Exception $e) {
            Log::error('Boarding validation failed - Unexpected error', [
                'escort_id' => $payload->get('escort_id') ?? null,
                'serial_number' => $request->serial_number,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Validation failed', 500);
        }
    }

    /**
     * Record passenger onboarding after validation
     */
    public function onboardPassenger(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'application_id' => 'required|integer|exists:bus_pass_applications,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get authenticated escort using JWT token (same as other methods)
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            // Verify this is an escort token
            if ($payload->get('type') !== 'escort') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid token type'
                ], 403);
            }

            $escortId = $payload->get('escort_id');
            $escort = Escort::with(['escortAssignment.busRoute', 'escortAssignment.livingInBus'])->find($escortId);

            if (!$escort) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Escort not authenticated'
                ], 401);
            }

            // Load escort with active assignment
            $activeAssignment = $escort->escortAssignment;

            if (!$activeAssignment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Escort has no active bus assignment'
                ], 403);
            }

            // Find bus pass application - allow applications with valid cards regardless of status
            $busPassApplication = BusPassApplication::where('id', $request->application_id)
                ->where(function ($query) {
                    $query->whereNotNull('branch_card_id')
                        ->orWhereNotNull('temp_card_qr');
                })
                ->whereNotIn('status', ['rejected', 'deactivated'])
                ->with(['person', 'establishment'])
                ->first();

            if (!$busPassApplication) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bus pass application not found, has no valid card, or is inactive'
                ], 400);
            }

            // Check for duplicate onboarding within 4 hours
            $recentOnboarding = Onboarding::where('bus_pass_application_id', $busPassApplication->id)
                ->where('onboarded_at', '>', now()->subHours(4))
                ->first();

            if ($recentOnboarding) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Passenger already onboarded within the last 4 hours'
                ], 409);
            }

            // Validate route authorization
            $isAuthorized = false;
            if ($activeAssignment->route_type === 'living_out' && $activeAssignment->busRoute) {
                $isAuthorized = $this->isBranchCardAllowedForRoute($busPassApplication, $activeAssignment->busRoute);
            } elseif ($activeAssignment->route_type === 'living_in' && $activeAssignment->livingInBus) {
                $isAuthorized = $this->isBranchCardAllowedForLivingInRoute($busPassApplication, $activeAssignment->livingInBus);
            }

            if (!$isAuthorized) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Passenger not authorized for this route'
                ], 403);
            }

            // Create onboarding record
            $onboarding = Onboarding::create([
                'bus_pass_application_id' => $busPassApplication->id,
                'escort_id' => $escort->id,
                'bus_route_id' => $activeAssignment->busRoute?->id,
                'living_in_bus_id' => $activeAssignment->livingInBus?->id,
                'route_type' => $activeAssignment->busRoute ? 'living_out' : 'living_in',
                'branch_card_id' => $busPassApplication->branch_card_id ?: $busPassApplication->temp_card_qr,
                'serial_number' => $busPassApplication->temp_card_qr,
                'onboarded_at' => now(),
                'boarding_data' => [
                    'escort_name' => $escort->name,
                    'escort_regiment_no' => $escort->regiment_no,
                    'route_name' => $activeAssignment->busRoute?->name ?: $activeAssignment->livingInBus?->name,
                    'passenger_name' => $busPassApplication->person?->name,
                    'establishment_name' => $busPassApplication->establishment?->name,
                ]
            ]);

            // Log the onboarding
            Log::info("Passenger onboarded", [
                'onboarding_id' => $onboarding->id,
                'application_id' => $busPassApplication->id,
                'escort_id' => $escort->id,
                'route_type' => $onboarding->route_type,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Passenger onboarded successfully',
                'data' => [
                    'onboarding_id' => $onboarding->id,
                    'onboarded_at' => $onboarding->onboarded_at,
                    'passenger_name' => $busPassApplication->person?->name,
                    'route_name' => $activeAssignment->busRoute?->name ?: $activeAssignment->livingInBus?->name,
                    'route_type' => $onboarding->route_type,
                ]
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token invalid or expired'
            ], 401);
        } catch (\Exception $e) {
            Log::error('Onboarding failed', [
                'application_id' => $request->application_id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Onboarding failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Decrypt serial number to extract branch card ID
     */
    protected function decryptSerialNumber(string $serialNumber): ?string
    {
        try {
            // Define the encryption key (should match the key used for encryption)
            $key = 'a12b34c56i78m90s'; // Replace with your actual encryption key

            $parts = explode(':', $serialNumber);

            if (count($parts) !== 2) {
                Log::error('Serial number decryption failed - Invalid format', [
                    'serial_number' => $serialNumber,
                ]);
                return null;
            }

            $decryptedData = openssl_decrypt(
                base64_decode($parts[0]),
                "aes-128-cbc",
                $key,
                OPENSSL_RAW_DATA,
                base64_decode($parts[1])
            );

            if ($decryptedData === false) {
                Log::error('Serial number decryption failed - OpenSSL error', [
                    'serial_number' => $serialNumber,
                    'error' => openssl_error_string(),
                ]);
                return null;
            }

            // Extract only the branch card ID (first part before the pipe separator)
            $dataParts = explode('|', $decryptedData);
            return $dataParts[0] ?? null;
        } catch (\Exception $e) {
            Log::error('Serial number decryption failed', [
                'serial_number' => $serialNumber,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Check if branch card is allowed for the escort's assigned bus route (living out)
     */
    protected function isBranchCardAllowedForRoute(BusPassApplication $application, BusRoute $assignedBusRoute): bool
    {
        // Check if the application has routes that match the assigned bus route
        $applicationRoutes = [];

        // Check requested bus name (for living out)
        if ($application->requested_bus_name) {
            $applicationRoutes[] = $application->requested_bus_name;
        }

        // Check weekend bus name (for living out)
        if ($application->weekend_bus_name) {
            $applicationRoutes[] = $application->weekend_bus_name;
        }

        // Check if any of the application routes match the assigned bus route
        return in_array($assignedBusRoute->name, $applicationRoutes);
    }

    /**
     * Check if branch card is allowed for the escort's assigned living in route
     */
    protected function isBranchCardAllowedForLivingInRoute(BusPassApplication $application, $assignedLivingInBus): bool
    {
        // For living in routes, check the living_in_bus field
        if ($application->living_in_bus) {
            return $application->living_in_bus === $assignedLivingInBus->name;
        }

        return false;
    }

    /**
     * Validate temporary card for boarding
     */
    public function validateTempCardBoarding(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'serial_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            // Verify this is an escort token
            if ($payload->get('type') !== 'escort') {
                return $this->forbiddenResponse('Invalid token type');
            }

            $escortId = $payload->get('escort_id');
            $escort = Escort::with(['escortAssignment.busRoute', 'escortAssignment.livingInBus'])->find($escortId);

            if (!$escort) {
                return $this->notFoundResponse('Escort not found');
            }

            // Check if escort has active assignment
            if (!$escort->escortAssignment) {
                Log::warning('Temp card boarding validation failed - No active assignment', [
                    'escort_id' => $escortId,
                    'serial_number' => $request->serial_number,
                ]);
                return $this->errorResponse('Escort has no active bus assignment', 403);
            }

            $assignment = $escort->escortAssignment;

            // Find bus pass application by temp_card_qr
            $busPassApplication = BusPassApplication::with(['person', 'establishment'])
                ->where('temp_card_qr', $request->serial_number)
                ->where('status', 'integrated_to_temp_card')
                ->first();

            if (!$busPassApplication) {
                Log::warning('Temp card boarding validation failed - Card not found or invalid', [
                    'escort_id' => $escortId,
                    'temp_card_qr' => $request->serial_number,
                ]);
                return $this->successResponse([
                    'allowed' => false,
                    'passenger_name' => null,
                    'passenger_image_url' => null,
                    'bus_route' => null,
                    'reason' => 'Temporary card not found or invalid'
                ], 'Boarding not allowed');
            }

            // Check if the temporary card is allowed for the escort's assigned route
            $isAllowed = false;
            $routeName = '';
            $routeId = null;

            if ($assignment->route_type === 'living_out' && $assignment->busRoute) {
                $isAllowed = $this->isTempCardAllowedForRoute($busPassApplication, $assignment->busRoute);
                $routeName = $assignment->busRoute->name;
                $routeId = $assignment->busRoute->id;
            } elseif ($assignment->route_type === 'living_in' && $assignment->livingInBus) {
                $isAllowed = $this->isTempCardAllowedForLivingInRoute($busPassApplication, $assignment->livingInBus);
                $routeName = $assignment->livingInBus->name;
                $routeId = $assignment->livingInBus->id;
            }

            Log::info('Temp card boarding validation completed', [
                'escort_id' => $escortId,
                'temp_card_qr' => $request->serial_number,
                'route_type' => $assignment->route_type,
                'route_id' => $routeId,
                'allowed' => $isAllowed,
            ]);

            // Generate full URL for person image
            $personImageUrl = null;
            if ($busPassApplication->person_image) {
                $personImageUrl = asset('storage/' . $busPassApplication->person_image);
            }

            return $this->successResponse([
                'allowed' => $isAllowed,
                'application_id' => $busPassApplication->id,
                'passenger_name' => $busPassApplication->person->name ?? 'Unknown',
                'passenger_image_url' => $personImageUrl,
                'bus_route' => $routeName,
                'reason' => $isAllowed ? null : 'Temporary card not authorized for this route'
            ], $isAllowed ? 'Boarding allowed' : 'Boarding not allowed');
        } catch (JWTException $e) {
            return $this->unauthorizedResponse('Token invalid or expired');
        } catch (\Exception $e) {
            Log::error('Temp card boarding validation failed - Unexpected error', [
                'escort_id' => $escortId,
                'temp_card_qr' => $request->serial_number,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Boarding validation failed', 500);
        }
    }

    /**
     * Check if temporary card is allowed for specific route
     */
    protected function isTempCardAllowedForRoute(BusPassApplication $application, $assignedRoute): bool
    {
        // Check if the application's bus matches the assigned route
        if ($application->requested_bus_name && $application->requested_bus_name === $assignedRoute->name) {
            return true;
        }

        if ($application->weekend_bus_name && $application->weekend_bus_name === $assignedRoute->name) {
            return true;
        }

        return false;
    }

    /**
     * Check if temporary card is allowed for living in route
     */
    protected function isTempCardAllowedForLivingInRoute(BusPassApplication $application, $assignedLivingInBus): bool
    {
        // For living in routes, check the living_in_bus field
        if ($application->living_in_bus) {
            return $application->living_in_bus === $assignedLivingInBus->name;
        }

        return false;
    }

    /**
     * Get onboarded passengers for a specific route
     */
    public function getOnboardedPassengers(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'route_id' => 'required|string',
                'route_type' => 'required|in:living_out,living_in',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get authenticated escort using JWT token
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            // Verify this is an escort token
            if ($payload->get('type') !== 'escort') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid token type'
                ], 403);
            }

            $escortId = $payload->get('escort_id');
            $escort = Escort::find($escortId);

            if (!$escort) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Escort not authenticated'
                ], 401);
            }

            $routeId = $request->route_id;
            $routeType = $request->route_type;

            // Determine time period based on current time
            $now = now();
            $isMorning = $now->hour < 12; // Before or at 12 PM
            $startTime = $now->copy()->startOfDay();
            $endTime = $now->copy()->endOfDay();

            if ($isMorning) {
                // Morning: from start of day to 12:00 PM
                $endTime = $now->copy()->startOfDay()->setHour(12)->setMinute(0)->setSecond(0);
                $timePeriod = 'morning';
            } else {
                // Afternoon: from 12:00 PM to end of day
                $startTime = $now->copy()->startOfDay()->setHour(12)->setMinute(0)->setSecond(0);
                $timePeriod = 'evening';
            }

            // Query onboardings based on route type and time period
            $query = Onboarding::where('escort_id', $escortId)
                ->where('route_type', $routeType)
                ->whereBetween('onboarded_at', [$startTime, $endTime])
                ->with([
                    'busPassApplication.person',
                    'busPassApplication.establishment',
                    'busRoute',
                    'escort'
                ]);

            if ($routeType === 'living_out') {
                $query->where('bus_route_id', $routeId);
            } else {
                $query->where('living_in_bus_id', $routeId);
            }

            $onboardings = $query->orderBy('onboarded_at', 'desc')->get();

            // Format passenger details
            $passengers = $onboardings->map(function ($onboarding) {
                return [
                    'onboarding_id' => $onboarding->id,
                    'passenger_name' => $onboarding->busPassApplication->person?->name ?? 'N/A',
                    'regiment_no' => $onboarding->busPassApplication->person->regiment_no,
                    'establishment_name' => $onboarding->busPassApplication->establishment?->name ?? 'N/A',
                    'bus_pass_type' => $onboarding->busPassApplication->bus_pass_type,
                    'branch_card_id' => $onboarding->branch_card_id,
                    'serial_number' => $onboarding->serial_number,
                    'onboarded_at' => $onboarding->onboarded_at->format('Y-m-d H:i:s'),
                    'route_name' => $onboarding->busRoute?->name ?? $onboarding->boarding_data['route_name'] ?? 'N/A',
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Onboarded passengers retrieved successfully for ' . $timePeriod,
                'data' => [
                    'route_id' => $routeId,
                    'route_type' => $routeType,
                    'time_period' => $timePeriod,
                    'escort_name' => $escort->name,
                    'total_passengers' => $passengers->count(),
                    'passengers' => $passengers
                ]
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token is invalid or expired'
            ], 401);
        } catch (\Exception $e) {
            Log::error('Error retrieving onboarded passengers', [
                'error' => $e->getMessage(),
                'route_id' => $request->route_id ?? null,
                'route_type' => $request->route_type ?? null,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve onboarded passengers'
            ], 500);
        }
    }
}
