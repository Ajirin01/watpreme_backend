<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Jobs\SendMessageJob;

class MessangerController extends Controller
{
    public function messanger(){
        return view('messanger');
    }

    public function sendMessage(Request $request)
    {
        // Obtain phone number and message content from the request
        $phoneNumber = $request->input('phone_number');
        $message = $request->input('text');
        $textId = $request->input('textId');

        // WhatsApp API endpoint URL
        $apiUrl = 'https://graph.facebook.com/v18.0/162047617002931/messages';

        // Prepare message data
        $postData = [
            "messaging_product"=> "whatsapp",    
            "recipient_type"=> "individual",
            "to"=> "+2347036998003",
            "type"=> "text",
            "text"=> [
                "preview_url"=> false,
                "body"=> $message
            ]
        ];

        // Send POST request to the WhatsApp API endpoint with authorization header
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('WHATSAPP_API_TOKEN'),
        ])->post($apiUrl, $postData);

        // return response()->json($response);

        // Check if the request was successful
        if ($response['messages'][0]['id']) {
            // Message sent successfully
            return response()->json(['status' => 'success', 'textId'=> $textId ]);
        } else {
            // Failed to send message
            return response()->json(['status' => 'error', 'message' => 'Failed to send message'], $response->status());
        }
    }

    public function sendMessageJob(Request $request){
        // Obtain phone number and message content from the request
        // $phoneNumber = $request->input('phone_number');
        // $message = $request->input('text');
        // $textId = $request->input('textId');

        $phoneNumber = "2347036998003";
        $message = "Sending Message from worker";
        $textId = "1323e2d32f23r1e1";

        for ($i=0; $i < 5; $i++) { 
           // Dispatch the SendMessageJob
            SendMessageJob::dispatch($phoneNumber, $message, $textId);
        }

        // Respond with a success message or redirect as needed
        return response()->json(['status' => 'success']);
    }
}
