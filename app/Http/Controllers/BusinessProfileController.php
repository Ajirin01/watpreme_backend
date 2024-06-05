<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\BusinessProfile;

class BusinessProfileController extends Controller
{
    public function index(){
        return response()->json(BusinessProfile::all(), 200);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            // Retrieve the business profile
            $businessProfile = BusinessProfile::findOrFail($id);

            return response()->json($businessProfile);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Business profile not found'], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'profilePicture' => 'nullable|string',
            'phoneNumber' => 'nullable|string',
            'about' => 'nullable|string',
            'businessAddress' => 'nullable|string',
            'businessDescription' => 'nullable|string',
            'businessEmail' => 'nullable|email',
            'businessIndustry' => 'nullable|string',
            'website1' => 'nullable|string',
            'website2' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            // Create a new business profile
            $businessProfile = BusinessProfile::create($request->all());

            return response()->json($businessProfile, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create business profile'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate the request data
        try {
            // Retrieve the business profile
            $businessProfile = BusinessProfile::findOrFail($id);

            // Update the business profile
            $businessProfile->update($request->all());

            return response()->json($businessProfile);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Business profile not found"], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating business profile', 'error'=> $e], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Retrieve the business profile
            $businessProfile = BusinessProfile::findOrFail($id);

            // Delete the business profile
            $businessProfile->delete();

            return response()->json(['message' => 'Business profile deleted']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Business profile not found"], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting business profile', 'error'=> $e], 500);
        }
    }

    public function queryBusinessProfiles(Request $request){
        // Retrieve the BusinessProfile records based on the request parameters
        $settings = BusinessProfile::where($request->all())->get();

        // Return the response
        return response()->json($settings, 200);
    }
}
