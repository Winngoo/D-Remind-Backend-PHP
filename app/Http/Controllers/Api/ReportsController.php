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
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ReportsController extends Controller
{
    public function UserPdfReport(Request $request)
    {

        $data = $this->getFilteredData($request);

        //return $data;

        if (empty($data->all())) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide parameters for the search.',
            ], 402);
        }

        $pdf = Pdf::loadView('reports.users-pdf', ['users' => $data]);
        return $pdf->download('UsersReport.pdf');
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
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }


        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        $data = User::whereBetween('created_at', [$startDate, $endDate])->get();

        $users = collect($data);
        //return $data;

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return Excel::download(new UsersExport($startDate, $endDate, $users), 'UsersReport.xlsx');
    }

    public function UserCsvReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }


        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        $users = User::whereBetween('created_at', [$startDate, $endDate])->get();

        return Excel::download(new UsersCsvExport($users), 'UsersReport.csv');

        //return $data ;
    }

    public function ReminderPdfReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date',
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
}
