<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Mail\ReminderNotification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */

    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $today = Carbon::now()->startOfDay();
            $currentTime = Carbon::now();

            // Log::info('Timezone: ' . date_default_timezone_get());
            // Log::info($currentTime);

            $reminders = Reminder::whereNotNull('due_date')
                ->where('due_date', '>=', $today)
                ->where('email_notification_status', 'active')
                ->get();

            //Log::info($reminders);

            foreach ($reminders as $reminder) {
                $user = $reminder->user;
                if (!$user) {
                    continue;
                }

                $dueDate = Carbon::parse($reminder->due_date)->startOfDay();
                $remainingDays = $dueDate->diffInDays($today);

                //Log::info($remainingDays);

                if ($dueDate->gt($today)) {
                    if ($remainingDays === 10 && is_null($reminder->email10days)) {
                        $this->sendReminder($user, $reminder, '10 days');
                        $reminder->update(['email10days' => now()]);
                    } elseif ($remainingDays === 5 && is_null($reminder->email5days)) {
                        $this->sendReminder($user, $reminder, '5 days');
                        $reminder->update(['email5days' => now()]);
                    } elseif ($remainingDays === 3 && is_null($reminder->email3days)) {
                        $this->sendReminder($user, $reminder, '3 days');
                        $reminder->update(['email3days' => now()]);
                    } elseif ($remainingDays === 1 && is_null($reminder->email1day)) {
                        $this->sendReminder($user, $reminder, '1 day');
                        $reminder->update(['email1day' => now()]);
                    }
                } elseif ($remainingDays === 0 && is_null($reminder->emailcurrentday)) {
                    $this->sendReminder($user, $reminder, 'Today');
                    $reminder->update(['emailcurrentday' => now(), 'email_notification_status' => 'completed']);
                }
            }
        })->everyMinute();
    }



    protected function sendReminder($user, $reminder, $timeLeft)
    {
        $emailData = [
            'title' => $reminder->title,
            'due_date' => Carbon::parse($reminder->due_date)->format('Y-m-d'),
            'time_left' => $timeLeft,
            'time' => $reminder->time,
            'category' => $reminder->category,
            'subcategory' => $reminder->subcategory,
            'provider' => $reminder->provider,
            'cost' => $reminder->cost,
            'description' => $reminder->description,
        ];

        Mail::to($user->email)->send(new ReminderNotification($emailData));

        Log::info('Sent reminder to: ' . $user->email . ' for ' . $timeLeft);
    }


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
