<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        $data = $request->validated();

        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $user = Auth::user();

            $token = $user->createToken('token')->plainTextToken;
            $username = $user->firstname;

            $response = [];

            $response['token'] = $token;
            $response['username'] = $username;

        }

        else {
            $response = [];

            $error = "User does not exist";

            $response['error'] = $error;

        }
        return json_encode($response);
    }
}
