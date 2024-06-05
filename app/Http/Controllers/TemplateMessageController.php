<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TemplateMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\MediaUpload;

class TemplateMessageController extends Controller
{
    public $code;

    public function index()
    {
        return response()->json(['data'=> TemplateMessage::all()], 200);
    }

    public function store(Request $request)
    {
        // return response()->json($request->all());

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'category' => 'required|string',
                'language' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(["errors" => $validator->errors()], 422);
            }

            $templateMessage = TemplateMessage::create($request->all());

            return response()->json($templateMessage, 201);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to create template message", "error" => $e], 500);
        }
    }

    public function show($id)
    {
        try {
            $templateMessage = TemplateMessage::findOrFail($id);
            return response()->json($templateMessage);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Template message not found"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to fetch template message"], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $templateMessage = TemplateMessage::findOrFail($id);
            $templateMessage->update($request->all());

            return response()->json($templateMessage, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Template message not found"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to update template message", "error" => $e], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $templateMessage = TemplateMessage::findOrFail($id);
            $templateMessage->delete();

            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Template message not found"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Failed to delete template message"], 500);
        }
    }

    public function uploadTemplate($id){
        // return response()->json(TemplateMessage::find($id), 200);
        $templateToUpload = TemplateMessage::find($id);

        $components = $templateToUpload->components;
        
        // check if header is type of media

        if($templateToUpload->components[0]['type'] === 'HEADER'  &&  $templateToUpload->components[0]['format'] !== 'TEXT'){
            // get the ID of the media we uploaded to our server so we can use to upload whatsapp resumeable upload
            $mediaID = $templateToUpload->components[0]['example']['header_handle'][0];

            // get the media data
            $mediaToUpload = MediaUpload::find($mediaID);

            // initialize an upload session
            $createSession = $this->createUploadSession($mediaToUpload);
            
            if($createSession){
                // upload the file with ID of the created session
                $uploadedFile = $this->UploadFileWithSession($createSession['id'], $mediaToUpload->url);

                // update component
                $components[0]['example']['header_handle'][0] = $uploadedFile['h'];
                $templateToUpload['components'] = $components;

                // now send the template message to the WhatsApp api for review
                $sentTemplate = $this->sendTemplate($templateToUpload);

                $templateToUpload->update(['status'=> $sentTemplate['status'], 'uploaded'=> true ]);

                return response()->json($templateToUpload, 200);
                // return response()->json($templateToUpload, 200);
            }


            // return response()->json( $createSession['id'], 200);

        }else{
            $sentTemplate = $this->sendTemplate($templateToUpload);
            // return response()->json($sentTemplate, 200);

            if(!$sentTemplate['error']){
                $templateToUpload->update(['status'=> $sentTemplate['status'], 'uploaded'=> true ]);

                return response()->json($sentTemplate, 200);
            }else{
                return response()->json($sentTemplate, 200);
            }
        }
    }

    public function uploadUpdateTemplate($id){
        // return response()->json(TemplateMessage::find($id), 200);
        $templateToUpload = TemplateMessage::find($id);

        $components = $templateToUpload->components;
        
        // check if header is type of media

        if($templateToUpload->components[0]['type'] === 'HEADER'  &&  $templateToUpload->components[0]['format'] !== 'TEXT'){
            // get the ID of the media we uploaded to our server so we can use to upload whatsapp resumeable upload
            $mediaID = $templateToUpload->components[0]['example']['header_handle'][0];

            // get the media data
            $mediaToUpload = MediaUpload::find($mediaID);

            // initialize an upload session
            $createSession = $this->createUploadSession($mediaToUpload);
            
            if($createSession){
                // upload the file with ID of the created session
                $uploadedFile = $this->UploadFileWithSession($createSession['id'], $mediaToUpload->url);

                // update component
                $components[0]['example']['header_handle'][0] = $uploadedFile['h'];
                $templateToUpload['components'] = $components;

                // now send the template message to the WhatsApp api for review
                $updatedTemplate = $this->updateTemplate($templateToUpload);

                $templateToUpload->update(['status'=> $updatedTemplate['status'], 'uploaded'=> true ]);

                return response()->json($templateToUpload, 200);
                // return response()->json($templateToUpload, 200);
            }
        }else{
            $sentTemplate = $this->updateTemplate($templateToUpload);
            // return response()->json($sentTemplate, 200);

            if(!$sentTemplate['error']){
                $templateToUpload->update(['status'=> $sentTemplate['status'], 'uploaded'=> true ]);

                return response()->json($sentTemplate, 200);
            }else{
                return response()->json($sentTemplate, 200);
            }
        }
    }

    function createUploadSession($data)
    {
        // WhatsApp API endpoint URL
        $apiUrl = 'https://graph.facebook.com/v18.0/1808635872903290/uploads';

        // Construct the POST data
        $postData = [
            'file_length' => $data->file_length,
            'file_type' => $data->file_type,
            'access_token' => env('WHATSAPP_API_TOKEN'), // Replace with your actual access token
        ];

        // Send POST request to create WhatsApp API upload session
        $response = Http::post($apiUrl, $postData);

        // Check if the request was successful
        if ($response->successful()) {
            // Upload session created successfully
            return $response->json();
        } else {
            // Failed to create upload session
            return ['error' => 'Failed to create upload session'];
        }
    }

    function UploadFileWithSession($uploadSessionId, $fileName)
    {
        // Construct the URL for the upload session
        $apiUrl = 'https://graph.facebook.com/v18.0/'. $uploadSessionId;

        try {
            // Send the POST request with the file using Laravel's Http facade
            $response = Http::withHeaders([
                'Authorization' => 'OAuth ' . env('WHATSAPP_API_TOKEN'),
                'file_offset' => 0,
            ])->attach(
                'file', // Name of the file field in the request
                fopen($_SERVER['DOCUMENT_ROOT']. $fileName, 'r'), // Open the file for reading
                basename($fileName) // Get the base name of the file
            )->post($apiUrl);

            // Check if the request was successful
            if ($response->status() == 200) {
                // File uploaded successfully
                return $response->json();
            } else {
                // Failed to upload file
                return ['error' => 'Failed to upload file'];
            }
        } catch (\Exception $e) {
            // Handle exceptions
            return ['error' => $e->getMessage()];
        }
    }

    function UploadFile(Request $request)
    {
        // Construct the URL for the upload session
        $apiUrl = 'https://graph.facebook.com/v18.0/'. $request->uploadSessionId;

        try {
            // Send the POST request with the file using Laravel's Http facade
            $response = Http::withHeaders([
                'Authorization' => 'OAuth ' . env('WHATSAPP_API_TOKEN'),
                'file_offset' => 0,
            ])->attach(
                'file', // Name of the file field in the request
                fopen($_SERVER['DOCUMENT_ROOT']. $request->fileName, 'r'), // Open the file for reading
                basename($request->fileName) // Get the base name of the file
            )->post($apiUrl);

            // Check if the request was successful
            if ($response->status() == 200) {
                // File uploaded successfully
                return $response->json();
            } else {
                // Failed to upload file
                return ['error' => 'Failed to upload file'];
            }
        } catch (\Exception $e) {
            // Handle exceptions
            return ['error' => $e->getMessage()];
        }
    }

    function sendTemplate($template){
        $templateData = $template->toArray();
        unset($templateData['id']);

        // Extract the 'code' from the 'Language' field if it exists
        if (isset($templateData['language']['code'])) {
            $templateData['language'] = $templateData['language']['code'];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('WHATSAPP_API_TOKEN'),
            'Content-Type' => 'application/json',
        ])->post('https://graph.facebook.com/v18.0/218912241301976/message_templates', $templateData);
        
        // Check if the request was successful
        if ($response->successful()) {
            // Request successful, handle the response
            $responseData = $response->json();
            // Process the response data as needed
            return $responseData;
        } else {
            // Request failed, handle the error
            $errorResponse = $response->json();
            // Process the error response as needed
            return $errorResponse;
        }
    }

    function updateTemplate($template){
        $name = $this->getWhatsappTemplateByName($template->name);
        $templateData = $template->toArray();
        unset($templateData['id']);
        unset($templateData['header']);
        unset($templateData['name']);
        unset($templateData['language']);


        // Extract the 'code' from the 'Language' field if it exists
        if (isset($templateData['language']['code'])) {
            $templateData['language'] = $templateData['language']['code'];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('WHATSAPP_API_TOKEN'),
            'Content-Type' => 'application/json',
        ])->post('https://graph.facebook.com/v18.0/'.$name, $templateData);
        
        // Check if the request was successful
        if ($response->successful()) {
            // Request successful, handle the response
            $responseData = $response->json();
            // Process the response data as needed
            return $responseData;
        } else {
            // Request failed, handle the error
            $errorResponse = $response->json();
            // Process the error response as needed
            return $errorResponse;
        }
    }

    public function syncTemplates()
    {
        try {
            // Fetch all templates from the WhatsApp API
            $whatsappTemplates = $this->fetchWhatsappTemplates();

            // return response()->json($whatsappTemplates);

            // Fetch all templates stored in the database
            $serverTemplates = TemplateMessage::all();

            // Compare and update or create templates
            $this->compareAndUpdateTemplates($whatsappTemplates, $serverTemplates);

            // return response()->json(['message' => 'Templates synchronized successfully', 'data' => $this->compareAndUpdateTemplates($whatsappTemplates, $serverTemplates)], 200);

            return response()->json(['message' => 'Templates synchronized successfully', 'data' => TemplateMessage::all()], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to synchronize templates: ' . $e], 500);
        }
    }

    private function fetchWhatsappTemplates()
    {
        // WhatsApp API endpoint URL
        $apiUrl = 'https://graph.facebook.com/v18.0/218912241301976/message_templates';

        // Send GET request to the WhatsApp API endpoint with authorization header
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('WHATSAPP_API_TOKEN'),
        ])->get($apiUrl);

        // Check if the request was successful
        if ($response->successful()) {
            // Return the response data
            return $response->json();
        } else {
            // Failed to fetch template messages
            throw new \Exception('Failed to fetch template messages');
        }
    }

    private function getWhatsappTemplateByName($name){
        // WhatsApp API endpoint URL
        $apiUrl = 'https://graph.facebook.com/v18.0/218912241301976/message_templates?name='.$name;

        // Send GET request to the WhatsApp API endpoint with authorization header
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('WHATSAPP_API_TOKEN'),
        ])->get($apiUrl);

        // Check if the request was successful
        if ($response->successful()) {
            // Return the response data
            return $response->json();
        } else {
            // Failed to fetch template messages
            throw new \Exception('Failed to fetch template messages');
        }
    }

    private function compareAndUpdateTemplates($whatsappTemplates, $serverTemplates)
    {
        // Load the contents of the supported_languages file
        $supportedLanguages = json_decode(file_get_contents(public_path('supported_languages.json')), true);

        foreach ($whatsappTemplates['data'] as $whatsappTemplate) {
            // Construct the language JSON object
            $languageJson = $this->constructLanguageJson($whatsappTemplate['language'], $supportedLanguages);
            // Check if the template exists in the server templates
            $serverTemplate = $serverTemplates->where('name', $whatsappTemplate['name'])->first();

            if ($serverTemplate) {
                // Update the status of the server template
                $serverTemplate->status = $whatsappTemplate['status'];
                $serverTemplate->components = $whatsappTemplate['components'];
                $serverTemplate->save();
            } else {
                // Create a new template entry
                TemplateMessage::create([
                    'name' => $whatsappTemplate['name'],
                    'category' => $whatsappTemplate['category'],
                    'status' => $whatsappTemplate['status'],
                    'language' => $languageJson, // Use the constructed language JSON object
                    'uploaded' => true,
                    'components' => $whatsappTemplate['components']
                    // Add other fields as needed
                ]);
            }
        }
    }

    private function constructLanguageJson($code, $supportedLanguages)
    {
        // Search for the language with the given code
        $foundLanguages = array_values(array_filter($supportedLanguages['supported_languages'], function ($language) use ($code) {
            return $language['code'] === $code;
        }));

        // Check if the language was found
        if (!empty($foundLanguages)) {
            // Construct the language JSON object
            $languageJson = [
                'language' => $foundLanguages[0]['language'],
                'code' => $foundLanguages[0]['code']
            ];

            return $languageJson;
        } else {
            // Language not found, handle this case accordingly (throw an exception, return a default language, etc.)
            return null;
        }
    }
}
