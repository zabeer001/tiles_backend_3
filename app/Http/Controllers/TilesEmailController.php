<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SimpleMail;

class TilesEmailController extends Controller
{
    public function sendMailWithCloudFile(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'file_url' => 'required|url',
        ]);

        $email = $request->email;
        $fileUrl = $request->file_url;

        Mail::to($email)->send(new SimpleMail($fileUrl));

        return response()->json(['message' => 'Email sent successfully.']);
    }

}
