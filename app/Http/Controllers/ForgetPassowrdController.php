<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\URL;

class ForgetPassowrdController extends Controller
{
    // public function sendResetEmailLink(Request $request)
    // {
    //     $request->validate(['email' => 'required|email']);
    //     $status = Password::sendResetLink($request->only('email'));

    //     if ($status === Password::RESET_LINK_SENT) {
    //         return response()->json([
    //             'message' => 'Password reset link sent successfully',
    //             'status' => $status
    //         ], 200);
    //     }

    //     return response()->json([
    //         'message' => __($status),
    //         'status' => $status
    //     ], 400);
    // }

    // app/Http/Controllers/Auth/PasswordResetController.php

    public function sendResetEmailLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 400);
    }
}
