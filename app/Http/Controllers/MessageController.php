<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\Business;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\Contact;
use Illuminate\Support\Facades\Http;

class MessageController extends Controller
{
    public function index(Conversation $conversation)
    {
        $messages = $conversation->messages()->get();
        // $messages = Message::with(['sender', 'receiver'])->get();
        return response()->json($messages);
    }

    public function store(Request $request, Conversation $conversation)
{
    // Manual validation
    $validator = Validator::make($request->all(), [
        'sender' => 'required|array',
        'receiver' => 'required|array',
        'message' => 'required|string',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Obtain phone number and message content from the request
    $phoneNumber = '';
    if ($request->sender['type'] === "operator") {
        $contactId = $request->receiver['id'];
        $contact = Contact::find($contactId);
        if ($contact) {
            $phoneNumber = $contact->phone;
        } else {
            return response()->json(['status' => 'error', 'message' => 'Contact not found'], 404);
        }
    } else {
        return response()->json(['status' => 'error', 'message' => 'Invalid sender type'], 400);
    }

    // WhatsApp API endpoint URL
    $apiUrl = env('WHATSAPP_API_BASE_URL_Phone') . 'messages';

    // Prepare message data
    $postData = [
        "messaging_product" => "whatsapp",
        "recipient_type" => "individual",
        "to" => $phoneNumber,
        "type" => "text",
        "text" => [
            "preview_url" => false,
            "body" => $request->message
        ]
    ];

    // Send POST request to the WhatsApp API endpoint with authorization header
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . env('WHATSAPP_API_TOKEN'),
    ])->post($apiUrl, $postData);

    // Check if the request was successful
    if ($response->successful() && isset($response['messages'][0]['id'])) {
        // Message sent successfully, save to the database
        $message = $conversation->messages()->create([
            'sender' => ['type' => $request->sender['type'], 'id' => $request->sender['id']],
            'receiver' => ['type' => $request->receiver['type'], 'id' => $request->receiver['id']],
            'message' => $request->message,
            'status' => 'sent'
        ]);

        return response()->json($message, 201);
    } else {
        // Failed to send message
        return response()->json(['status' => 'error', 'message' => 'Failed to send message'], $response->status());
    }
}


    public function storeFromWebhook(Request $request)
    {
        // Manual validation
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|exists:contacts,id',
            'message' => 'required|string',
            'business_id' => 'required|exists:businesses,id',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $contactId = $request->contact_id;
        $messageText = $request->message;
        $businessId = $request->business_id;

        if($request->sender['type'] === "user"){
            $contactId = $request->receiver['id'];
            $contact = Contact::find($contactId);
            // $phoneNumber = $contact->phone;

            $business = Business::find($businessId); // Assuming you have a business ID
            $adminUserId = Business::find($businessId)->businessAdminUsers[0]->id;
            $adminUser = $business->getAdminUserById($adminUserId);

            if ($adminUser) {
                // Admin user found, do something with it
            } else {
                // Admin user not found
            }
        }

        // Find or create a conversation for the contact and business
        $conversation = Conversation::firstOrCreate([
            'contact_id' => $contactId,
            'business_id' => $businessId,
            'status' => 'active'
        ], [
            'start_time' => now(),
            'end_time' => now()->addHours(24),
            'uuid' => (string) Str::uuid(),
        ]);

        $message = $conversation->messages()->create([
            'sender' => ['type'=> 'user', 'id'=> $contactId],
            'receiver' => ['type'=> 'operator', 'id'=> $adminUserId],
            'message' => $messageText,
            'status' => 'sent'
        ]);

        return response()->json($message, 201);
    }

    public function show(Conversation $conversation, Message $message)
    {
        return response()->json($message);
    }

    public function update(Request $request, Conversation $conversation, Message $message)
    {
        $request->validate([
            'message' => 'sometimes|string',
        ]);

        $message->update($request->only(['message']));
        return response()->json($message);
    }

    public function destroy(Conversation $conversation, Message $message)
    {
        $message->delete();
        return response()->json(null, 204);
    }
}
