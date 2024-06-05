<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MediaUpload;

class MediaUploadController extends Controller
{
    public function index(){
        return response()->json(MediaUpload::all(), 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'file' => 'required|file|max:10240', // Example: Max file size of 10MB
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Generate a unique name for the file
            $fileName = time().'_'.$file->getClientOriginalName();

            
            // Get file details
            $fileSize = $file->getSize(); // File size in bytes
            $fileType = $file->getClientMimeType(); // File type (e.g., image/jpeg)

            // Move the file to the uploads directory
            $file->move(public_path('uploads'), $fileName);

            // Save the file details to the database
            $mediaUpload = MediaUpload::create([
                'url' => '/uploads/'.$fileName, // Example: Store relative path to the file
                'name' => $fileName,
                'file_length' => $fileSize,
                'file_type' => $fileType,
            ]);

            return response()->json(['message' => 'File uploaded successfully', 'data' => $mediaUpload], 201);
        } else {
            return response()->json(['message' => 'No file uploaded'], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request data
        $request->validate([
            'file' => 'required|file|max:10240', // Example: Max file size of 10MB
        ]);

        // Find the media upload record
        $mediaUpload = MediaUpload::findOrFail($id);

        // Handle file update
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Generate a unique name for the file
            $fileName = time().'_'.$file->getClientOriginalName();

            // Move the file to the uploads directory
            $file->move(public_path('uploads'), $fileName);

            // Update the file details in the database
            $mediaUpload->update([
                'url' => '/uploads/'.$fileName, // Example: Store relative path to the file
                'name' => $fileName,
            ]);

            return response()->json(['message' => 'File updated successfully', 'data' => $mediaUpload]);
        } else {
            return response()->json(['message' => 'No file uploaded'], 400);
        }
    }

    public function show(string $id)
    {
        // Find the media upload record
        $mediaUpload = MediaUpload::findOrFail($id);

        return response()->json(['data' => $mediaUpload]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the media upload record
        $mediaUpload = MediaUpload::findOrFail($id);

        // Delete the file from the storage
        if (file_exists(public_path($mediaUpload->url))) {
            unlink(public_path($mediaUpload->url));
        }

        // Delete the media upload record from the database
        $mediaUpload->delete();

        return response()->json(['message' => 'Media upload deleted successfully']);
    }

    // Create a whatsapp api upload Session
    public function createSession(Request $request, $file)
    {
        // WhatsApp API endpoint URL
        $apiUrl = 'https://graph.facebook.com/v18.0/1808635872903290/uploads';

        // Construct the POST data
        $postData = [
            'file_length' => $file->getSize(),
            'file_type' => $file->getClientMimeType(),
            'access_token' => env('WHATSAPP_API_TOKEN'), // Replace with your actual access token
        ];

        // Send POST request to create WhatsApp API upload session
        $response = Http::post($apiUrl, $postData);

        // Check if the request was successful
        if ($response->successful()) {
            // Upload session created successfully
            return response()->json($response->json());
        } else {
            // Failed to create upload session
            return response()->json(['error' => 'Failed to create upload session'], $response->status());
        }
    }
}
