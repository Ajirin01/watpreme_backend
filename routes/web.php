<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// Access token for your app
// (copy token from DevX getting started page
// and save it as environment variable into the .env file)
// $token = env('WHATSAPP_TOKEN');

// $token = "ajirin";
$token = env('WHATSAPP_TOKEN');

// Sets server port and logs message on success
Route::post('/webhook', function (Request $request) use ($token) {
    // Parse the request body from the POST
    $body = $request->all();

    // Check the Incoming webhook message
    info(json_encode($request->all(), JSON_PRETTY_PRINT));

    // info on WhatsApp text message payload: https://developers.facebook.com/docs/whatsapp/cloud-api/webhooks/payload-examples#text-messages
    if ($request->has('object')) {
        $entry = $request->input('entry.0');
        if (
            isset($entry['changes']) &&
            isset($entry['changes'][0]['value']['messages'][0])
        ) {
            $phoneNumberId = $entry['changes'][0]['value']['metadata']['phone_number_id'];
            $from = $entry['changes'][0]['value']['messages'][0]['from'];
            $msgBody = $entry['changes'][0]['value']['messages'][0]['text']['body'];

            // Send message
            Http::post("https://graph.facebook.com/v12.0/{$phoneNumberId}/messages?access_token={$token}", [
                'messaging_product' => 'whatsapp',
                'to' => $from,
                'text' => ['body' => "Ack: {$msgBody}"]
            ]);
        }
        return response()->json([], 200);
    } else {
        // Return a '404 Not Found' if event is not from a WhatsApp API
        return response()->json(['error' => 'Not Found'], 404);
    }
});

// Accepts GET requests at the /webhook endpoint. You need this URL to setup webhook initially.
// info on verification request payload: https://developers.facebook.com/docs/graph-api/webhooks/getting-started#verification-requests 
Route::get('/webhook', function (Request $request) {
    /**
     * UPDATE YOUR VERIFY TOKEN
     * This will be the Verify Token value when you set up webhook
     **/
    $verifyToken = env('VERIFY_TOKEN');

    // Parse params from the webhook verification request
    $mode = $request->query('hub_mode');
    $token = $request->query('hub_verify_token');
    $challenge = $request->query('hub_challenge');
    
    // Check if a token and mode were sent
    if ($mode && $token) {
        // Check the mode and token sent are correct
        if ($mode === 'subscribe' && $token === $verifyToken) {
            // Respond with 200 OK and challenge token from the request
            info('WEBHOOK_VERIFIED');
            return response($challenge, 200);
        } else {
            // Responds with '403 Forbidden' if verify tokens do not match
            return response()->json(['error' => 'Forbidden'], 403);
        }
    }
});


Route::get('/', function () {
    return view('welcome');
});
