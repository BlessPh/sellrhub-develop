<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
            event(new Registered($user));
        }

        $request->user()->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user,
        ]);
    }

    public function updateProfileImage(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);



        if ($request->hasFile('image')) {
            try {
                // Store the image in the 'user/images' directory in public storage
                $path = $request->file('image')->store('user/images', 'public');

                // Create a new image record in the user's image table
                $user->images()->create([
                    'url' => Storage::url($path),
                ]);
            } catch (\Exception $e) {
                // Log the error for debugging purposes
                Log::error("Error uploading user image: " . $e->getMessage());
                // Optional: Return an error response if necessary
            }
        }


        $user->load('images');

        return response()->json([
            'message' => 'Image updated successfully.',
        ], 201);
    }
}
