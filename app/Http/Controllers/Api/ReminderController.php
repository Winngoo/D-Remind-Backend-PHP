<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Support\Facades\Log;
use Exception;
use Kreait\Firebase\Exception\FirebaseException;



class ReminderController extends Controller
{
    public function index(Request $request, $id)
    {
        $reminders = Reminder::select('id', 'user_id', 'title', 'category', 'subcategory', 'due_date', 'time', 'description', 'provider', 'cost', 'payment_frequency')->where('user_id', $id)->get();
        return response()->json(['success' => true, 'data' => $reminders], 200);
    }

    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:3|max:100',
            'category' => 'required|string',
            'subcategory' => 'required|string',
            'due_date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'description' => 'nullable|string|max:100',
            'provider' => 'nullable|string|max:50',
            'cost' => 'nullable|numeric',
            'payment_frequency' => 'nullable|in:Monthly,Quarterly,Half-Yearly,Annually',
            // 'device_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // $deviceToken = $request->device_token;

        // dd($deviceToken);

        $category = Category::where('name', $request->category)->first();

        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found.'], 404);
        }

        $subcategory = SubCategory::where('name', $request->subcategory)
            ->where(function ($query) use ($category, $id) {
                $query->where('category_id', $category->id)
                    ->whereNull('user_id')
                    ->orWhere('user_id', $id);
            })
            ->first();

        if (!$subcategory) {
            $subcategory = SubCategory::create([
                'name' => $request->subcategory,
                'category_id' => $category->id,
                'user_id' => $id,
            ]);
        }

        $reminder = Reminder::create([
            'user_id' => $id,
            'title' => $request->title,
            'category' => $request->category,
            'subcategory' => $request->subcategory,
            'due_date' => $request->due_date,
            'time' => $request->time,
            'description' => $request->description,
            'provider' => $request->provider,
            'cost' => $request->cost,
            'payment_frequency' => $request->payment_frequency,
        ]);

        // $this->sendNotificationToAdmin($deviceToken, $reminder);

        return response()->json(['success' => true, 'data' => $reminder], 201);
    }


    // protected function sendNotificationToAdmin($deviceToken, $reminder)
    // {


    //     // dd(storage_path('app/firebase_key.json'));

    //     try {
    //         $credentialsPath = storage_path('app/firebase_key.json');

    //         if (!file_exists($credentialsPath)) {
    //             throw new Exception("Service account file does not exist at: {$credentialsPath}");
    //         }

    //         if (!is_readable($credentialsPath)) {
    //             throw new Exception("Service account file is not readable: {$credentialsPath}");
    //         }

    //         $messaging = (new Factory)
    //             ->withServiceAccount($credentialsPath)
    //             ->createMessaging();

    //         $message = CloudMessage::new()
    //             ->withTarget('token', $deviceToken)
    //             ->withNotification([
    //                 'title' => 'New Reminder Created',
    //                 'body' => "Reminder: {$reminder->title} is created by User ID {$reminder->user_id}.",
    //             ])
    //             ->withData([
    //                 'reminder_id' => $reminder->id,
    //                 'user_id' => $reminder->user_id,
    //             ]);

    //         $messaging->send($message);

    //         Log::info('Firebase notification sent successfully.', [
    //             'reminder_id' => $reminder->id,
    //             'user_id' => $reminder->user_id,
    //             'device_token' => $deviceToken,
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Failed to send Firebase notification.', [
    //             'error' => $e->getMessage(),
    //             'reminder_id' => $reminder->id,
    //             'user_id' => $reminder->user_id,
    //             'device_token' => $deviceToken,
    //         ]);
    //     }
    // }

    public function show($id, Request $request)
    {
        $token = $request->bearerToken();

        $userToken = UserToken::where('token', $token)->first();

        if (!$userToken) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $reminder = Reminder::select('id', 'user_id', 'title', 'category', 'subcategory', 'due_date', 'time', 'description', 'provider', 'cost', 'payment_frequency')
            ->where('id', $id)
            ->where('user_id', $userToken->user_id)
            ->first();

        if (!$reminder) {
            return response()->json(['success' => false, 'message' => 'Reminder not found or access denied'], 404);
        }

        return response()->json(['success' => true, 'data' => $reminder], 200);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reminder_id' => 'required',
            'title' => 'required|string|min:3|max:100',
            'category' => 'required|string',
            'subcategory' => 'required|string',
            'due_date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'description' => 'nullable|string|max:100',
            'provider' => 'nullable|string|max:50',
            'cost' => 'nullable|numeric',
            'payment_frequency' => 'nullable|in:Monthly,Quarterly,Half-Yearly,Annually',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $reminder = Reminder::select('id', 'user_id', 'title', 'category', 'subcategory', 'due_date', 'time', 'description', 'provider', 'cost', 'payment_frequency')->where('id', $request->reminder_id)->where('user_id', $id)->first();

        if (!$reminder) {
            return response()->json(['success' => false, 'message' => 'Reminder not found'], 404);
        }

        $reminder->update($request->only([
            'title',
            'category',
            'subcategory',
            'due_date',
            'time',
            'description',
            'provider',
            'cost',
            'payment_frequency',
        ]));

        return response()->json(['success' => true, 'message' => 'Reminder updated successfully ', 'data' => $reminder], 200);
    }

    public function destroy($id)
    {
        $reminder = Reminder::where('id', $id)->first();

        if (!$reminder) {
            return response()->json(['success' => false, 'message' => 'Reminder not found'], 404);
        }

        $reminder->delete();
        return response()->json(['success' => true, 'message' => 'Reminder deleted successfully'], 200);
    }
}
