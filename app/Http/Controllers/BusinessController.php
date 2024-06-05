<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Http;
use App\Models\User;

class BusinessController extends Controller
{
    public function index()
    {
        $businesses = Business::all();
        return response()->json($businesses, 200);
    }

    public function show($id)
    {
        try {
            $business = Business::findOrFail($id);
            return response()->json($business);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Business not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'phone_number_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Make GET request to WhatsApp API endpoint
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('WHATSAPP_API_TOKEN'),
        ])->get('https://graph.facebook.com/v19.0/'.$request->input('phone_number_id'));

        // Check if the request was successful
        if ($response->successful()) {
            // Extract necessary information from the response
            $phoneNumberId = $response['id'];
            $displayName = $response['verified_name'];
            $phoneNumber = $response['display_phone_number'];

            // Create the business
            $business = Business::create([
                'name' => $displayName, // Assuming the business name is provided in the request
                'phone_number_id' => $request->input('phone_number_id'),
            ]);

            // Create the user for the business
            $user = User::create([
                'name' => $displayName,
                'email' => $request->input('email'),
                'phone' => $phoneNumber,
                'role' => 'business_admin',
                'password' => bcrypt($request->input('email')), // Assuming email is used as password
                'business_id' => $business->id,
            ]);

            // Return the business and user data as JSON response
            return response()->json([
                'business' => $business,
                'user' => $user,
            ], 201);
        } else {
            // Failed to fetch data from WhatsApp API
            return response()->json(['message' => 'Failed to fetch data from WhatsApp API'], $response->status());
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            // 'user_id' => 'exists:users,id',
            'name' => 'string',
            'phone_number_id' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $business = Business::findOrFail($id);
            $business->update($request->all());
            return response()->json($business);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Business not found"], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating business', 'error'=> $e], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $business = Business::findOrFail($id);
            $business->delete();
            return response()->json(['message' => 'Business deleted']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Business not found"], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting business', 'error'=> $e], 500);
        }
    }
}
