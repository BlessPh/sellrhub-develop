<?php

namespace App\Http\Controllers\SecurityLogin;

use App\Http\Controllers\Controller;
use App\Models\SecurityLogin;
use Illuminate\Http\Request;

class SecurityLoginController extends Controller
{
    public function index()
    {
        $logs = SecurityLogin::with('user')->get();

        return response()->json($logs);
    }
}
