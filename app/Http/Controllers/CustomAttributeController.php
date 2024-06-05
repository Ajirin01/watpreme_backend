<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomAttribute;
use Illuminate\Support\Facades\Validator;

class CustomAttributeController extends Controller
{
    public function index()
    {
        try {
            $customAttributes = CustomAttribute::all();
            return response()->json($customAttributes, 200);
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

            $customAttribute = CustomAttribute::create($validator->validated());

            return response()->json($customAttribute, 201);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to create custom attribute", "error" => $e], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $customAttribute = CustomAttribute::findOrFail($id);
            return response()->json($customAttribute, 200);
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

            $customAttribute = CustomAttribute::findOrFail($id);
            $customAttribute->update($validator->validated());

            return response()->json($customAttribute, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Custom attribute not found"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to update custom attribute", "error" => $e], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $customAttribute = CustomAttribute::findOrFail($id);
            $customAttribute->delete();

            return response()->json(['message' => 'Record deleted'], 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Custom attribute not found"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to delete custom attribute"], 500);
        }
    }
}
