<?php

namespace App\Http\Controllers;

use App\Mail\SendMAil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendMailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $data = [
          'email' => $request->input('email'),
          'name' => $request->input('name'),
          'subject' => $request->input('subject'),
          'body' => $request->input('body'),
        ];
    
  
      Mail::to('support@freesoulijaza.com')->send(new SendMAil($data));
  
      return response()->json(['message' => 'Email sent successfully!']);
    }
}
