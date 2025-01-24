<?php

namespace App\Http\Controllers\TwoFactorAuth;

use App\Http\Controllers\Controller;
use App\Http\Requests\TwoFactorAuth\TwoFactorAuthRequest;
use App\Models\TwoFactorAuth;
use http\Env\Response;
use Illuminate\Http\Request;

class TwoFactorAuthController extends Controller
{
    public function __verify(TwoFactorAuthRequest $request)
    {
        $towFA = TwoFactorAuth::where('user_id', $request->user_id)->where(
            'code', $request->code
        )->firstOrFail();

        if (!$towFA->isValid())
        {
            return response()->json([
                'message' => 'Code or expired invalid'
            ], 400);
        }

        $towFA->update(['verified_at' => now()]);

        return Response()->json([
            'message' => 'Verified successfully'
        ]);
    }
}
