<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Reminder;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function ActiveUsersCount()
    {
        $users_count = User::where('status', 'active')->count();
        //dd($users_count);
        $members_count = User::count();
        //dd($members_count);

        return response()->json(['success' => true, 'data' => ['active_users_count' => $users_count , 'total_members_count' => $members_count]], 200);
    }

    public function LatestRegistrationList()
    {
        $latest_users = User::latest()->take(10)->select('id', 'full_name', 'email', 'created_at')->get();
        //dd($latest_users);

        return response()->json(['success' => true, 'latest_register_users' => $latest_users], 200);
    }

    public function TotalRemindersCount()
    {
        $reminders_count = Reminder::where('reminder_status', 'active')->count();
        //dd($reminders_count);

        return response()->json(['success' => true, 'total_reminders_count' => $reminders_count], 200);
    }

    public function PopularCategoryList()
    {
        $popular_categories = Reminder::select('category', DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->orderByDesc('count')
            ->take(3)
            ->get();

        return response()->json([
            'success' => true,
            'popular_categories' => $popular_categories,
        ], 200);
    }

    public function SubscriptionRevenueDetails()
    {
        $currentYear = date('Y');

        $monthlyRevenue = Subscription::select(
            DB::raw('MONTH(payment_date) as month'),
            DB::raw('SUM(amount) as total_revenue')
        )
            ->whereYear('payment_date', $currentYear)
            ->groupBy(DB::raw('MONTH(payment_date)'))
            ->orderBy(DB::raw('MONTH(payment_date)'))
            ->get();

        $overallRevenue = $monthlyRevenue->sum('total_revenue');

        $revenueDetails = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenueDetails[] = [
                'month' => date('F', mktime(0, 0, 0, $i, 1)),
                'revenue' => $monthlyRevenue->firstWhere('month', $i)->total_revenue ?? 0,
            ];
        }

        return response()->json([
            'success' => true,
            'year' => $currentYear,
            'monthly_revenue' => $revenueDetails,
            'overall_revenue' => $overallRevenue,
        ], 200);
    }

    public function ReminderBannersCount()
    {
        $totalRemindersCount = Reminder::count();

        $todayRemindersCount = Reminder::whereDate('due_date', today())->count();

        $upcomingRemindersCount = Reminder::whereDate('due_date', '>', today())->count();

        $completedRemindersCount = Reminder::where('reminder_status', 'inactive')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_reminders' => $totalRemindersCount,
                'today_reminders' => $todayRemindersCount,
                'upcoming_reminders' => $upcomingRemindersCount,
                'completed_reminders' => $completedRemindersCount,
            ],
        ], 200);
    }

    
}
