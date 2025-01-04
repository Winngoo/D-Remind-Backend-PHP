<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Reminder;
use Illuminate\Support\Facades\Mail;

if (!function_exists('sendReminderNotification')) {
    function sendReminderNotification()
    {
        $reminders = Reminder::whereNotNull('due_date')
            ->whereBetween('due_date', [
                Carbon::now()->addDays(6)->startOfDay(),
                Carbon::now()->endOfDay(),
            ])
            ->get();

        foreach ($reminders as $reminder) {
            $user = User::find($reminder->user_id);

            if (!$user) {
                continue;
            }

            $dueDate = Carbon::parse($reminder->due_date);
            $now = Carbon::now();

            if ($dueDate->diffInDays($now) <= 6 && $dueDate->diffInDays($now) >= 0) {
                $emailData = [
                    'title' => $reminder->title,
                    'due_date' => $dueDate->format('Y-m-d'),
                    'time' => $reminder->time,
                    'category' => $reminder->category,
                    'subcategory' => $reminder->subcategory,
                    'provider' => $reminder->provider,
                    'cost' => $reminder->cost,
                    'description' => $reminder->description,
                ];

                Mail::to($user->email)->send(new \App\Mail\ReminderNotification($emailData));
            }
        }
    }
}
