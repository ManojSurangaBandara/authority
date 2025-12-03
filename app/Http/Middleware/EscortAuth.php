<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class EscortAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            // Check if this is an escort token
            if ($payload->get('type') !== 'escort') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Access denied. Escort authentication required.'
                ], 403);
            }

            // Check if escort exists
            $escortId = $payload->get('escort_id');
            $escort = \App\Models\Escort::find($escortId);

            if (!$escort) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Escort not found.'
                ], 403);
            }

            // Add escort to request for easy access in controllers
            $request->attributes->set('escort', $escort);
            $request->attributes->set('escort_token_payload', $payload);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token invalid or expired'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication failed'
            ], 401);
        }

        return $next($request);
    }
}
