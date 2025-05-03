<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgetPassowrdController extends Controller
{
    public function sendResetEmailLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Password reset link sent successfully',
                'status' => $status
            ], 200);
        }

        return response()->json([
            'message' => __($status),
            'status' => $status
        ], 400);
    }
    public function resetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Password reset link sent successfully',
                'status' => $status
            ], 200);
        }

        return response()->json([
            'message' => __($status),
            'status' => $status
        ], 400);
    }
}
