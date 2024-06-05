<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Retrieve all tickets from the database
            $tickets = Ticket::all();
            // Return a JSON response with the tickets
            return response()->json($tickets, 200);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to fetch tickets"], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'priority' => 'required|string|max:50',
                'status' => 'required|string|max:50',
                'user_id' => 'required|string'
                // You may need additional validation rules based on your requirements
            ]);

            if ($validator->fails()) {
                return response()->json(["errors" => $validator->errors()], 422);
            }

            // Create a new ticket with the validated data
            $ticket = Ticket::create($validator->validated());

            // Return a JSON response with the newly created ticket
            return response()->json($ticket, 201);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to create ticket", "error" => $e], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            // Find the ticket by its ID
            $ticket = Ticket::findOrFail($id);
            // Return a JSON response with the ticket
            return response()->json($ticket, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Ticket not found"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to fetch ticket"], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'title' => 'string|max:255',
                'description' => 'string',
                'priority' => 'string|max:50',
                'status' => 'string|max:50',
                // You may need additional validation rules based on your requirements
            ]);

            if ($validator->fails()) {
                return response()->json(["errors" => $validator->errors()], 422);
            }

            // Find the ticket by its ID
            $ticket = Ticket::findOrFail($id);

            // Update the ticket with the validated data
            $ticket->update($validator->validated());

            // Return a JSON response with the updated ticket
            return response()->json($ticket, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Ticket not found"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to update ticket", "error" => $e], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Find the ticket by ID
            $ticket = Ticket::findOrFail($id);

            // Delete the ticket
            $ticket->delete();

            // Return a success response
            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Ticket not found"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to delete ticket"], 500);
        }
    }
}
