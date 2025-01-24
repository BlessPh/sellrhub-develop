<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Notifications\WelcomeUserNotification;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterRequest $request)
    {
        $data = $request->validated();

        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        $token = $user->createToken('token')->plainTextToken;

        $user->notify(new WelcomeUserNotification($user));

        $response = [];

        $response['message'] = 'User created successfully';
        $response['token'] = $token;
        $response['data'] = $user;

        return json_encode($response);
    }
}
