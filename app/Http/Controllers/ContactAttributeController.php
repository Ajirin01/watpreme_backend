<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactAttribute;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ContactAttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $attributes = ContactAttribute::with('customAttribute')->get();
            return response()->json($attributes, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch contact attributes'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $attribute = ContactAttribute::create($request->all());
            $attribute->load('customAttribute');
            return response()->json($attribute, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create contact attribute'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $attribute = ContactAttribute::findOrFail($id);
            return response()->json($attribute, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Contact attribute not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch contact attribute'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $attribute = ContactAttribute::findOrFail($id);
            $attribute->update($request->all());
            $attribute->load('customAttribute');
            return response()->json($attribute, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Contact attribute not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update contact attribute'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $attribute = ContactAttribute::findOrFail($id);
            $attribute->delete();
            return response()->json(['message' => 'Contact attribute deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Contact attribute not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete contact attribute'], 500);
        }
    }
}
