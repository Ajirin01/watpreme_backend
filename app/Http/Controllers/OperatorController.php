<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Operator;
use Illuminate\Support\Facades\Validator;
use Hash;
use App\Models\User;


class OperatorController extends Controller
{
    public function index()
    {
        try {
            $operators = Operator::all();
            return response()->json($operators, 200);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to fetch operators"], 500);
        }
    }
    
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                // 'user_id' => 'required|integer',
                'business_id' => 'required|integer',
                'team_id' => 'required|integer',
                'name' => 'required',
                'email' => 'required',
                'phone'=> 'required'
            ]);
            

            if ($validator->fails()) {
                return response()->json(["errors" => $validator->errors()], 422);
            }

            $userData = [];
            $userData['name'] = $request->name;
            $userData['phone'] = $request->phone;
            $userData['email'] = $request->email;
            // $userData['role'] = $request->operator;
            $userData['role'] = 'operator';

            $userData['business_id'] = $request->business_id;
            $userData['password'] = Hash::make($request->email);

            $user = User::create($userData);

            if($user){
                $operatorData = [];
                $operatorData['team_id'] = $request->team_id;
                $operatorData['business_id'] = $request->business_id;
                $operatorData['user_id'] = $user->id;

                $operator = Operator::create($operatorData);

                return response()->json(Operator::find($operator->id), 201);
            }else{
                return response()->json(["message" => "Failed to create operator because user could not be created"], 500);
            }
            
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to create operator", "error"=> $e], 500);
        }
    }
    
    public function show(string $id)
    {
        try {
            $operator = Operator::findOrFail($id);
            return response()->json($operator, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Operator not found"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to fetch operator"], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        // return response()->json($request->all());
        try {
            // Find the operator by ID
            $operator = Operator::findOrFail($id);

            // Validate the request data
            $validator = Validator::make($request->all(), [
                'team_id' => 'required|integer',
                'name' => 'required',
                'email' => 'required',
                'phone'=> 'required'
            ]);

            // If validation fails, return validation errors
            if ($validator->fails()) {
                return response()->json(["errors" => $validator->errors()], 422);
            }

            // Update user data
            $operator->user->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'role' => $request->operator, // Assuming 'operator' field exists in the request
            ]);

            // Update operator data
            $operator->update([
                'team_id' => $request->team_id,
                'business_id' => $request->business_id,
            ]);

            // Return the updated operator
            return response()->json(Operator::find($operator->id), 200);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json(["message" => "Failed to update operator", "error"=> $e->getMessage()], 500);
        }
    }

    
    public function destroy(string $id)
    {
        try {
            $operator = Operator::findOrFail($id);
            $operator->delete();

            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Operator not found"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to delete operator"], 500);
        }
    }
    
    public function queryOperators(Request $request){
        // Retrieve the GeneralSetting records based on the request parameters
        $settings = Operator::where($request->all())->get();

        // Return the response
        return response()->json($settings, 200);
    }
}
