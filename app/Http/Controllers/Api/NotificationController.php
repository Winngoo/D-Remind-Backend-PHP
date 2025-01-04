<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Reminder;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function EmailNotificationList(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'reminder_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $reminders = Reminder::where('user_id', $userId)->where('id', $request->reminder_id)->where('reminder_status', 'active')->where('email_notification_status', 'active')->get();
        //return $request->reminder_id;

        $notifications = [];
        $now = Carbon::now();

        foreach ($reminders as $reminder) {
            $dueDate = Carbon::parse($reminder->due_date);
            $intervals = [
                'email10days' => 10,
                'email5days' => 5,
                'email3days' => 3,
                'email1day' => 1,
                'emailcurrentday' => 0,
            ];

            foreach ($intervals as $column => $days) {
                if ($reminder->$column !== null) {
                    $sentDate = Carbon::parse($reminder->$column);
                    $notifications[] = [
                        'message' => "Your '{$reminder->title}'s {$days} day reminder notification was sent to your mail on {$sentDate->format('Y-m-d')}.",
                        // 'sent_at' => $sentDate,
                    ];
                }
            }
        }
        return response()->json([
            'status' => 'success',
            'notifications' => $notifications,
        ], 200);
    }



    public function ReminderNotificationsUser($userId)
    {
        $currentDate = Carbon::now();
        $reminders = Reminder::where('user_id', $userId)
            ->where('reminder_status', 'active')
            ->get();

        //return($currentDate);

        $notifications = [];

        foreach ($reminders as $reminder) {

            $dueDate = Carbon::parse($reminder->due_date, 'UTC');
            $dueDate->setTimezone(config('app.timezone'));
            //return($dueDate);

            $daysRemaining = $currentDate->copy()->startOfDay()->diffInDays($dueDate->copy()->startOfDay(), false);
            //return($daysRemaining);

            if ($daysRemaining === 10) {
                $notifications[] = [
                    'message' => "Your '{$reminder->title}' has 10 days remaining.",
                    // 'reminder_id' => $reminder->id,
                    'days_remaining' => 10,
                ];
            } elseif ($daysRemaining === 5) {
                $notifications[] = [
                    'message' => "Your '{$reminder->title}' has 5 days remaining.",
                    // 'reminder_id' => $reminder->id,
                    'days_remaining' => 5,
                ];
            } elseif ($daysRemaining === 3) {
                $notifications[] = [
                    'message' => "Your '{$reminder->title}' has 3 days remaining.",
                    // 'reminder_id' => $reminder->id,
                    'days_remaining' => 3,
                ];
            } elseif ($daysRemaining === 1) {
                $notifications[] = [
                    'message' => "Your '{$reminder->title}' has 1 day remaining.",
                    // 'reminder_id' => $reminder->id,
                    'days_remaining' => 1,
                ];
            } elseif ($daysRemaining === 00 && $currentDate->isSameDay($dueDate)) {
                $notifications[] = [
                    'message' => "Final reminder for '{$reminder->title}' today!",
                    // 'reminder_id' => $reminder->id,
                    'days_remaining' => 0,
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'notifications' => $notifications,
        ]);
    }


    public function EmailNotificationControl(Request $request, $reminderid)
    {
        $validated = $request->validate([
            'status' => 'required|boolean',
        ]);
    
        $reminder = Reminder::find($reminderid);
    
        if (!$reminder) {
            return response()->json(['message' => 'Reminder not found'], 404);
        }
    
        if ($validated['status'] === true) {
            $reminder->email_notification_status = "active"; 
        } else {
            $reminder->email_notification_status = "inactive"; 
        }
    
        $reminder->save();
    
        return response()->json([
            'message' => 'Email notification status updated successfully',
            'reminder_id' => $reminder->id,
            'email_notification_status' => $reminder->email_notification_status,
        ]);
    }

    public function SMSNotificationControl(Request $request, $reminderid)
    {
        $validated = $request->validate([
            'status' => 'required|boolean',
        ]);
    
        $reminder = Reminder::find($reminderid);
    
        if (!$reminder) {
            return response()->json(['message' => 'Reminder not found'], 404);
        }
    
        if ($validated['status'] === true) {
            $reminder->sms_notification_status = "active"; 
        } else {
            $reminder->sms_notification_status = "inactive";
        }
    
        $reminder->save();
    
        return response()->json([
            'message' => 'SMS notification status updated successfully',
            'reminder_id' => $reminder->id,
            'sms_notification_status' => $reminder->sms_notification_status,
        ]);
    }
}
