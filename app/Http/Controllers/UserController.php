<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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



        if ($request->hasFile('image'))
        {
            $image = $request->file('image');
            $randomName = Str::random(30) . '.' . $image->getClientOriginalExtension();
            $path = Storage::disk('public')->put("user/images", $image);
            $user->images()->create([
                'url' => "storage/" . $path,
            ]);
        }

        $user->load('images');

        return response()->json([
            'message' => 'Image updated successfully.',
        ], 201);
    }
}
