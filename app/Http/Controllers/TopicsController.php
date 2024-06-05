<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic;
use Illuminate\Support\Facades\Validator;

class TopicsController extends Controller
{
    public function index()
    {
        try {
            $topics = Topic::all();
            return response()->json($topics, 200);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to fetch custom attributes"], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(["errors" => $validator->errors()], 422);
            }

            $topic = Topic::create($validator->validated());

            return response()->json($topic, 201);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to create custom attribute", "error" => $e], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $topic = Topic::findOrFail($id);
            return response()->json($topic, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Custom attribute not found"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to fetch custom attribute"], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(["errors" => $validator->errors()], 422);
            }

            $topic = Topic::findOrFail($id);
            $topic->update($validator->validated());

            return response()->json($topic, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Custom attribute not found"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to update custom attribute", "error" => $e], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $topic = Topic::findOrFail($id);
            $topic->delete();

            return response()->json(['message' => 'Record deleted'], 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Custom attribute not found"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to delete custom attribute"], 500);
        }
    }
}
