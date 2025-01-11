<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Reminders;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Exports\UsersCsvExport;
use App\Exports\ReminderExport;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ReportsController extends Controller
{
    // public function UserPdfReport(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'start_date' => 'required|date',
    //         'end_date' => 'required|date|after_or_equal:start_date',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
    //     $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

    //     $data = User::whereBetween('created_at', [$startDate, $endDate])->get();

    //     // $data = $this->getFilteredData($request);
    //     //return $data;

    //     if (empty($data->all())) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Please provide parameters for the search.',
    //         ], 402);
    //     }

    //     $pdf = Pdf::loadView('reports.users-pdf', ['users' => $data]);
    //     return $pdf->download('UsersReport.pdf');
    // }

    public function UserPdfReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $this->getFilteredData($request);

        if ($data->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No data found for the provided parameters.',
            ], 404);
        }

        $pdf = Pdf::loadView('reports.users-pdf', ['users' => $data]);

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="UsersReport.pdf"',
        ]);
    }


    private function getFilteredData(Request $request)
    {

        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        return User::whereBetween('created_at', [$startDate, $endDate])->get();
    }


    public function UserExcelReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

        $data = User::whereBetween('created_at', [$startDate, $endDate])->get();
        //return $data;

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'No data found'
            ], 404);
        }

        return Excel::download(new UsersExport($startDate, $endDate), 'UsersReport.xlsx');
    }

    public function UserCsvReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }


        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        $data = User::whereBetween('created_at', [$startDate, $endDate])->get();
        //return $data ;

        $csvData = [];
        $csvData[] = ['ID', 'Full Name', 'Email', 'Phone Number', 'Postcode', 'Country', 'Status'];

        foreach ($data as $user) {
            $csvData[] = [
                $user->id,
                $user->full_name,
                $user->email,
                $user->phone_number,
                $user->postcode,
                $user->country,
                $user->status,
            ];
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="UsersReport.csv"',
        ];

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');

            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function ReminderPdfReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        $data = Reminder::whereBetween('created_at', [$startDate, $endDate])->get();
        //return $data;

        $pdf = Pdf::loadView('reports.reminders-pdf', ['reminders' => $data])
            ->setPaper('a4', 'landscape');

        return $pdf->download('RemindersReport.pdf');
    }


    public function ReminderExcelReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

        $data = Reminder::with('user:id,full_name')->whereBetween('created_at', [$startDate, $endDate])->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'No data found'
            ], 404);
        }

        $flattenedData = $data->map(function ($reminder) {
            return [
                'id' => $reminder->id,
                'user_id' => $reminder->user_id,
                'user_name' => $reminder->user->full_name ?? 'N/A',
                'title' => $reminder->title,
                'category' => $reminder->category,
                'subcategory' => $reminder->subcategory,
                'due_date' => $reminder->due_date,
                'time' => $reminder->time,
                'description' => $reminder->description,
                'provider' => $reminder->provider,
                'cost' => $reminder->cost,
                'payment_frequency' => $reminder->payment_frequency,
                'reminder_status' => $reminder->reminder_status,
            ];
        });

        //return $flattenedData;

        return Excel::download(new ReminderExport($startDate, $endDate), 'RemindersReport.xlsx');
    }

    public function ReminderCsvReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }


        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        $data = Reminder::with('user:id,full_name')->whereBetween('created_at', [$startDate, $endDate])->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'No data found'
            ], 404);
        }

        $flattenedData = $data->map(function ($reminder) {
            return [
                'id' => $reminder->id,
                'user_id' => $reminder->user_id,
                'user_name' => $reminder->user->full_name ?? 'N/A',
                'title' => $reminder->title,
                'category' => $reminder->category,
                'subcategory' => $reminder->subcategory,
                'due_date' => $reminder->due_date,
                'time' => $reminder->time,
                'description' => $reminder->description,
                'provider' => $reminder->provider,
                'cost' => $reminder->cost,
                'payment_frequency' => $reminder->payment_frequency,
                'reminder_status' => $reminder->reminder_status,
            ];
        });

        //return $flattenedData ;

        $csvData = [];
        $csvData[] = ['Reminder ID', 'User Name', 'Title', 'Category', 'Sub Category', 'Due Date', 'Time', 'Description', 'Provider', 'Cost', 'Payment Frequency', 'Status'];

        foreach ($flattenedData as $reminder) {
            $csvData[] = [
                $reminder['id'],
                $reminder['user_name'],
                $reminder['title'],
                $reminder['category'],
                $reminder['subcategory'],
                $reminder['due_date'],
                $reminder['time'],
                $reminder['description'],
                $reminder['provider'],
                $reminder['cost'],
                $reminder['payment_frequency'],
                $reminder['reminder_status'],
            ];
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="RemindersReport.csv"',
        ];

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');

            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
