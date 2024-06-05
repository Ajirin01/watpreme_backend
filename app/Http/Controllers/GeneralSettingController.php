<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\GeneralSetting;

class GeneralSettingController extends Controller
{
    public function index(){
        return response()->json(GeneralSetting::all(), 200);
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
            // Retrieve the general setting
            $generalSetting = GeneralSetting::findOrFail($id);

            return response()->json($generalSetting);
        } catch (\Exception $e) {
            return response()->json(['message' => 'General setting not found'], 404);
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
        // return response()->json($request->all());
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|exists:businesses,id',
            'smsEnabled' => 'nullable|boolean',
            'timezone' => 'nullable|string',
            'language' => 'nullable|string',
            'displayLogo' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            // Create a new general setting
            $generalSetting = GeneralSetting::create($request->all());

            return response()->json($generalSetting, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create general setting', 'error'=> $e], 500);
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
        // return response()->json($request->all());
        try {
            // Retrieve the general setting
            $generalSetting = GeneralSetting::findOrFail($id);
            
            $generalSetting->update($request->all());

            return response()->json($generalSetting);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "General setting not found"], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update general setting', 'error'=> $e], 500);
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
            // Retrieve the general setting
            $generalSetting = GeneralSetting::findOrFail($id);

            // Delete the general setting
            $generalSetting->delete();

            return response()->json(['message' => 'General setting deleted']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "General setting not found"], 404);
        }  catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete general setting'], 500);
        }
    }

    public function queryGeneralSettings(Request $request){
        // Retrieve the GeneralSetting records based on the request parameters
        $settings = GeneralSetting::where($request->all())->get();

        // Return the response
        return response()->json($settings, 200);
    }
}
