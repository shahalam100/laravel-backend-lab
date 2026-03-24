<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;

class MailController extends Controller
{
    public function sendMail(Request $request){

        // Validation
        $request->validate([
            'email' => 'required|email',
        ]);

        //Get Email
        $email = $request->email;

        //Dispatch Job
        SendEmailJob::dispatch($email);

        //return response
        return response()->json([
            'message' => 'Email job dispatched successfully'
            ]);
    }
}
