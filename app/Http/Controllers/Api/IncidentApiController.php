<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incident;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class IncidentApiController extends Controller
{
    /**
     * Report a new incident.
     */
    public function report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'incident_type_id' => 'required|exists:incident_types,id',
            'description' => 'required|string|max:1000',
            'images' => 'nullable|array|max:3',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120', // max 5MB per image
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('incidents', 'public');
                $imagePaths[] = $path;
            }
        }

        $incident = Incident::create([
            'incident_type_id' => $request->incident_type_id,
            'description' => $request->description,
            'image1' => $imagePaths[0] ?? null,
            'image2' => $imagePaths[1] ?? null,
            'image3' => $imagePaths[2] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Incident reported successfully',
            'data' => $incident
        ], 201);
    }
}
