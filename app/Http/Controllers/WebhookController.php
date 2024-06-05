<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use Pusher\Pusher;

use App\Models\WebhookData;


// $token = "ajirin";
$token = env('WHATSAPP_TOKEN');

class WebhookController extends Controller
{
    public function webhookPost(Request $request){
        // Trigger Pusher event
        $pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true
        ]);
        $pusher->trigger('whatsapp-events', 'message-received', ['message' => $body['entry'][0]['changes'][0]['value']]);

        WebhookData::create(['data'=> $body]);
    }

    public function webhookGet(Request $request) {
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
    }
}
