<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class Feedbackcontroller extends Controller
{
    public function index($userid)
    {
        $feedbacks = Feedback::select('id', 'name', 'email', 'title', 'description', 'reply')->where('user_id', $userid)->get();

        if ($feedbacks->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No feedback found for the user'], 404);
        }

        return response()->json(['success' => true, 'data' => $feedbacks], 200);
    }

    public function store(Request $request, $userid)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $feedback = Feedback::create([
            'user_id' => $userid,
            'name' => $request->name,
            'email' => $request->email,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        $admin = Admin::first();
        if ($admin) {
            $emailData = [
                'title' => $feedback->title,
                'description' => $feedback->description,
                'name' => $feedback->name,
                'email' => $feedback->email,
            ];

            Mail::send('emails.feedback', $emailData, function ($message) use ($admin) {
                $message->to($admin->email)
                    ->subject('New Feedback Received');
            });
        }

        return response()->json(['success' => true, 'message' => 'Feedback created successfully', 'data' => $feedback], 201);
    }

    public function show($id)
    {
        $feedback = Feedback::select('id', 'name', 'email', 'title', 'description', 'reply')->where('id', $id)->first();

        if (!$feedback) {
            return response()->json(['success' => false, 'message' => 'Feedback not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $feedback], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $feedback = Feedback::find($id);

        if (!$feedback) {
            return response()->json(['success' => false, 'message' => 'Feedback not found'], 404);
        }

        $feedback->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json(['success' => true, 'message' => 'Feedback updated successfully', 'data' => $feedback], 200);
    }

    public function destroy($id)
    {
        $feedback = Feedback::find($id);

        if (!$feedback) {
            return response()->json(['success' => false, 'message' => 'Feedback not found'], 404);
        }

        $feedback->delete();

        return response()->json(['success' => true, 'message' => 'Feedback deleted successfully'], 200);
    }


    public function ViewFeedbackForAdmin()
    {
        $feedbacks = Feedback::select('id', 'name', 'email', 'title', 'description', 'reply')->get();

        if ($feedbacks->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No feedback found for the user'], 404);
        }

        return response()->json(['success' => true, 'data' => $feedbacks], 200);
    }

    public function ReplyToFeedback(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reply' => 'required|string',
        ]);

        $feedback = Feedback::find($id);

        if (!$feedback) {
            return response()->json(['success' => false, 'message' => 'Feedback not found'], 404);
        }

        $feedback->update([
            'reply' => $request->reply,
        ]);

        return response()->json(['success' => true, 'message' => 'Replied to feedback successfully', 'data' => $feedback], 200);
    }
    

    public function EditFeedbackReply(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reply' => 'required|string',
        ]);

        $feedback = Feedback::find($id);

        if (!$feedback) {
            return response()->json(['success' => false, 'message' => 'Feedback not found'], 404);
        }

        $feedback->update([
            'reply' => $request->reply,
        ]);

        return response()->json(['success' => true, 'message' => 'Feedback reply updated successfully', 'data' => $feedback], 200);
    }


    public function DeleteFeedbackReply($id)
    {
        $feedback = Feedback::find($id);

        if (!$feedback) {
            return response()->json(['success' => false, 'message' => 'Feedback not found'], 404);
        }

        $feedback->update([
            'reply' => Null,
        ]);

        return response()->json(['success' => true, 'message' => 'Feedback reply deleted successfully'], 200);
    }

}
