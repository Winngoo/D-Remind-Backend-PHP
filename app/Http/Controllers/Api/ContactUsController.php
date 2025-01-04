<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\ContactUs;
use App\Mail\ContactUsMail;

class ContactUsController extends Controller
{
    public function CreateContactUs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        $contact = ContactUs::create($validatedData);

        // Log::info($contact);
        


        Mail::to('balamuruga2210@gmail.com')->send(new ContactUsMail($contact));

        // Mail::raw('This is a test email', function ($message) {
        //     $message->to('balamuruga2210@gmail.com')->subject('Test Email');
        // });

        return response()->json(['message' => 'Contact form submitted successfully'], 201);
    }

    public function ViewContactUs()
    {
        $contact = ContactUs::select('id', 'first_name', 'last_name', 'email', 'message')->get();

        if ($contact->isEmpty()) {
            return response()->json(['message' => 'ContactUs not found'], 404);
        }

        return response()->json(['message' => 'ContactUs fetched successfully', 'data' => $contact], 201);

    }

    public function EditContactUs(Request $request, $id)
    {
        $contact = ContactUs::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Contact not found'], 404);
        }

        $validator = Validator::make($request->all(), [
           'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $contact->update($request->all());
        return response()->json(['message' => 'ContactUs updated successfully', 'data' => $contact], 200);
    }
}
