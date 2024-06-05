<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teams = Team::all();
        return response()->json($teams, 200);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'default' => 'required|boolean',
                'size' => 'required|integer',
            ]
        );

        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $team = Team::create($request->all());

        return response()->json($team, 201);
    }

    public function show(string $id)
    {
        $team = Team::find($id);
        return response()->json($team, 200);
    }

    public function update(Request $request, string $id)
    {
        try {
            // Find the team by its ID
            $team = Team::findOrFail($id);
            
            // Update the team with the validated data
            $team->update($request->all());

            // Return a JSON response with the updated team
            return response()->json($team, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Return a response indicating that the team was not found
            return response()->json(["message" => "Team not found"], 404);
        } catch (\Exception $e) {
            // Return a response indicating that an error occurred
            return response()->json(["message" => "An error occurred while updating the team"], 500);
        }
    }
  
    public function destroy(string $id)
    {
        try {
            $team = Team::findOrFail($id);
            $team->delete();
            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Team not found"], 404);
        }
    }

    public function queryTeams(Request $request){
        // Retrieve the GeneralSetting records based on the request parameters
        $settings = Team::where($request->all())->get();

        // Return the response
        return response()->json($settings, 200);
    }
}
