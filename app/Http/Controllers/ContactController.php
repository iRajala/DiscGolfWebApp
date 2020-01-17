<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contact;
use App\Mail\ContactMe;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            "name" => "required",
            "email" => "required|email",
            "content" => "required",
        ]);

        $contact = Contact::create($request->all());
        Mail::to('darayy@gmail.com')->send(new ContactMe($contact));

        if(count(Mail::failures()) > 0 ) {
                return response('Something went wrong');
            }
        else{
                return response()->json($contact, 201);  
            }    
    }

    public function show($id)
    {
        return Contact::findOrFail($id);
    }
}
