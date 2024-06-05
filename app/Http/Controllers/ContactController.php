<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\Contact;
use App\Models\CustomAttribute;

class ContactController extends Controller
{
    public function index()
    {
        try {
            // $contacts = Contact::all();

            $contacts = Contact::with('attributes', 'attributes.customAttribute')->get();

            return response()->json($contacts, 200);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to fetch contacts"], 500);
        }
    }

    // public function store(Request $request)
    // {
    //     // Define validation rules
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'phone' => [
    //             'required',
    //             'string',
    //             'max:20',
    //             // Check if the phone number is unique in the contacts table
    //             Rule::unique('contacts', 'phone'),
    //         ],
    //         'status' => 'required|string|max:50',
    //         'custom_attributes' => 'nullable|array',
    //     ]);

    //     // Check if validation fails
    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }
    //     // 'contact_attribute_id'
    //     // Create the contact
    //     $contact = Contact::create([
    //         'name' => $request->input('name'),
    //         'phone' => $request->input('phone'),
    //         'status' => $request->input('status'),
    //         'broadcast' => $request->input('broadcast'),
    //         'sms' => $request->input('sms')
    //     ]);

    //     return response()->json($contact, 201);
    // }

    public function store(Request $request)
    {
        // Define validation rules for each contact in the array
        $validator = Validator::make($request->all(), [
            '*.name' => 'required|string|max:255',
            '*.phone' => [
                'required',
                'string',
                'max:20',
                // Check if the phone number is unique in the contacts table
                Rule::unique('contacts', 'phone'),
            ],
            '*.status' => 'required|string|max:50',
            '*.custom_attributes' => 'nullable|array',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $contacts = [];

        // Loop through each contact in the request
        foreach ($request->all() as $contactData) {
            // Create the contact
            $contact = Contact::create([
                'name' => $contactData['name'],
                'phone' => $contactData['phone'],
                'status' => $contactData['status'],
                'broadcast' => $contactData['broadcast'] ?? false, // Set default value if not provided
                'sms' => $contactData['sms'] ?? false, // Set default value if not provided
            ]);

            $contacts[] = $contact;
        }

        return response()->json($contacts, 201);
    }
    
    public function show(string $id)
    {
        try {
            $contact = Contact::findOrFail($id);

            // Decode the custom attributes JSON string to an array of IDs
            $customAttributeIds = $contact->custom_attributes;

            // Fetch the corresponding custom attribute objects
            $customAttributes = CustomAttribute::whereIn('id', $customAttributeIds)->get();

            // Replace the array of IDs with the custom attribute objects
            $contact->custom_attributes = $customAttributes;

            return response()->json($contact, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Contact not found"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to fetch contact"], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'phone' => 'string|max:20',
            'status' => 'string|max:50',
            'custom_attributes' => 'nullable|array',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the contact by ID
        $contact = Contact::findOrFail($id);

        // Update the contact attributes
        $contact->name = $request->input('name', $contact->name);
        $contact->phone = $request->input('phone', $contact->phone);
        $contact->status = $request->input('status', $contact->status);
        $contact->sms = $request->input('sms', $contact->sms);
        $contact->broadcast = $request->input('broadcast', $contact->broadcast);

        // Check if custom_attributes exist in the request
        if ($request->has('custom_attributes')) {
            // Encode the custom_attributes array to JSON
            $contact->custom_attributes = $request->input('custom_attributes');
        }

        // Save the updated contact
        $contact->save();

        // Return a JSON response with the updated contact
        return response()->json($contact, 200);
    }
    
    public function destroy(string $id)
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->delete();

            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Contact not found"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to delete contact"], 500);
        }
    }
}
