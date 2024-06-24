<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BroadcastReport;

class BroadcastMetricsController extends Controller
{
    public function getMetrics($broadcastId)
    {
        $data = [
            ['label' => 'Messages Sent', 'value' => 83],
            ['label' => 'Messages Delivered', 'value' => 30],
            ['label' => 'Messages Read', 'value' => 83],
            ['label' => 'Messages Responded', 'value' => 50]
        ];

        return response()->json($data);
        // Validate the broadcast ID
        $metrics = BroadcastReport::where('broadcast_id', $broadcastId)->first();

        if (!$metrics) {
            return response()->json(['message' => 'Broadcast not found'], 404);
        }

        $response = [
            'Messages Sent' => $metrics->messages_sent,
            'Messages Delivered' => $metrics->messages_delivered,
            'Messages Read' => $metrics->messages_read,
            'Messages Responded' => $metrics->messages_responded
        ];

        return response()->json($response);
    }

    public function storeWebhookData(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'broadcast_id' => 'required|exists:broadcasts,id',
            'messages_sent' => 'required|integer',
            'messages_delivered' => 'required|integer',
            'messages_read' => 'required|integer',
            'messages_responded' => 'required|integer',
        ]);

        // Create or update the broadcast report
        BroadcastReport::updateOrCreate(
            ['broadcast_id' => $validated['broadcast_id']],
            $validated
        );

        return response()->json(['message' => 'Report data saved successfully']);
    }
}
