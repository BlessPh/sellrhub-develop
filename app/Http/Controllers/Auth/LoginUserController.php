<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginUserController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        // $request->session()->regenerate();

        $user = $request->user();

        $data = [
            'user' => [
                'full name' => $user->firstname . ' ' . $user->lastname,
                'email' => $user->email,
                'verified' => $user->email_verified_at
            ],
            'token' => $user->createToken($user->email)->plainTextToken
        ];

        // return ;

        return response()->json([
            $data
        ], 201);
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout()
    {
        /*
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        */

        $user = Auth::user();

        $user->currentAccessToken()->delete();

        return response()->json([
            'Logged out successfully'
        ]) && redirect('/');
    }

    public function logoutFromAllDevices(Request $request)
    {
        $user = $request->user();

        $user->tokens()->delete();

        return response()->json([
            'success'
        ]);
    }
}
