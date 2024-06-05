<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Broadcast;
use Illuminate\Support\Facades\Validator;

class BroadcastController extends Controller
{
    public function index()
    {
        try {
            $broadcasts = Broadcast::all();
            return response()->json($broadcasts, 200);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to fetch broadcasts"], 500);
        }
    }
    
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'business_id' => 'required|integer',
                'template_id' => 'required|integer',
                'channel' => 'required|string|max:255',
                'status' => 'required|string|max:255',
                'recipients' => 'required|array',
                'sent_date' => 'nullable|date',
                'is_scheduled' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json(["errors" => $validator->errors()], 422);
            }

            $broadcast = Broadcast::create([
                'name' => $request->input('name'),
                'business_id' => $request->input('business_id'),
                'template_id' => $request->input('template_id'),
                'channel' => $request->input('channel'),
                'status' => $request->input('status'),
                'recipients' => json_encode($request->input('recipients')),
                'sent_date' => $request->input('sent_date'),
                'is_scheduled' => $request->input('is_scheduled')
            ]);

            return response()->json($broadcast, 201);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to create broadcast", "error" => $e], 500);
        }
    }
    
    public function show(string $id)
    {
        try {
            $broadcast = Broadcast::findOrFail($id);
            return response()->json($broadcast, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Broadcast not found"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to fetch broadcast"], 500);
        }
    }
    
    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'business_id' => 'sometimes|required|integer',
                'template_id' => 'sometimes|required|integer',
                'channel' => 'sometimes|required|string|max:255',
                'status' => 'sometimes|required|string|max:255',
                'recipients' => 'sometimes|required|array',
                'sent_date' => 'nullable|date',
                'is_scheduled' => 'sometimes|required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json(["errors" => $validator->errors()], 422);
            }

            $broadcast = Broadcast::findOrFail($id);
            $broadcast->update($request->all());

            return response()->json($broadcast, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Broadcast not found"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to update broadcast", "error" => $e], 500);
        }
    }
    
    public function destroy(string $id)
    {
        try {
            $broadcast = Broadcast::findOrFail($id);
            $broadcast->delete();

            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Broadcast not found"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to delete broadcast"], 500);
        }
    }

    public function queryBroadcasts(Request $request){
        // Retrieve the GeneralSetting records based on the request parameters
        $broadcasts = Broadcast::where($request->all())->get();

        // Return the response
        return response()->json($broadcasts, 200);
    }
}
