<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Client\RequestException;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Http;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phoneNumber;
    protected $message;
    protected $textId;

    /**
     * Create a new job instance.
     */
    public function __construct($phoneNumber, $message, $textId)
    {
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
        $this->textId = $textId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // WhatsApp API endpoint URL
        $apiUrl = 'https://graph.facebook.com/v18.0/162047617002931/messages';

        // Prepare message data
        $postData = [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $this->phoneNumber,
            "type" => "text",
            "text" => [
                "preview_url" => false,
                "body" => $this->message
            ]
        ];

        try {
            // Send POST request to the WhatsApp API endpoint with authorization header
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('WHATSAPP_API_TOKEN'),
            ])->post($apiUrl, $postData);

            // Check if the request was successful
        if ($response->successful()) {
            // Message sent successfully
            Log::info('Message sent successfully to ' . $this->phoneNumber);
        } else {
            // Failed to send message
            Log::error('Failed to send message to ' . $this->phoneNumber . ': ' . $response->status());
        }
        } catch (RequestException $e) {
            // Handle request exception
            Log::error('Failed to send message to ' . $this->phoneNumber . ': ' . $e->getMessage());
        }
    }

}
