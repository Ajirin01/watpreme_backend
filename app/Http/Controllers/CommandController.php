<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class CommandController extends Controller
{
    public function run(Request $request)
    {
        // return response()->json(Hash::make("sin2@+cos2@1"));
        // Validate request inputs
        $request->validate([
            'password' => 'required',
            'command' => 'required',
        ]);

        // Retrieve the hashed password from the database
        $storedPassword = env('COMMAND_PASSWORD'); // Fetch the hashed password from the database

        // Compare the provided password with the hashed password
        if (!Hash::check($request->password, $storedPassword)) {
            return redirect()->back()->with('error', 'Incorrect password.');
        }

        // Execute the Artisan command
        $output = Artisan::call($request->command);

        // Redirect back with success message and command output
        return redirect()->back()->with('success', 'Command executed successfully. Output: ' . $output);
    }
}
