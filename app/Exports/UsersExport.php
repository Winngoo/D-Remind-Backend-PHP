<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use Carbon\Carbon;
use App\Models\User;


// class UsersExport implements FromCollection, WithEvents
// {

//     protected $startDate;
//     protected $endDate;
//     protected $headers;

//     public function __construct($startDate, $endDate)
//     {
//         $this->startDate = $startDate;
//         $this->endDate = $endDate;
//         $this->headers = ['S.No', 'User-Id', 'Name', 'Email', 'Phone Number', 'Postcode', 'Country', 'Status'];
//     }

//     /**
//      * Return the collection of data.
//      */
//     public function collection()
//     {
//         $data = User::whereBetween('created_at', [$this->startDate, $this->endDate]);

//         //dd($data);

//         return $data->get();
//     }

//     /**
//      * Return column headings.
//      */
//     // public function headings(): array
//     // {
//     //     return [
//     //         'ID',
//     //         'Full Name',
//     //         'Email',
//     //         'Phone Number',
//     //         'Postcode',
//     //         'Country',
//     //         'Status',
//     //     ];
//     // }

//     // /**
//     //  * Apply styles to the worksheet.
//     //  */
//     // public function styles(Worksheet $sheet)
//     // {
//     //     $sheet->getStyle('1:1')->getFont()->setBold(true); 
//     // }

//     // /**
//     //  * Automatically adjust column widths.
//     //  */
//     // public function registerEvents(): array
//     // {
//     //     return [
//     //         \Maatwebsite\Excel\Events\AfterSheet::class => function (\Maatwebsite\Excel\Events\AfterSheet $event) {
//     //             foreach (range('A', $event->sheet->getHighestColumn()) as $column) {
//     //                 $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
//     //             }
//     //         },
//     //     ];
//     // }

//     // private function setHeaders(Sheet $sheet, $headers)
//     // {
//     //     $headerRow = 1;
//     //     $columnIndex = 'A';

//     //     foreach ($headers as $header) {
//     //         $sheet->setCellValue($columnIndex . $headerRow, $header);
//     //         $columnIndex++;
//     //     }
//     // }

//     // public function registerEvents(): array
//     // {
//     //     return [
//     //         AfterSheet::class => function (AfterSheet $event) {


//     //             $this->headers = ['User-Id', 'Name', 'Email', 'Phone Number', 'Postcode', 'Country', 'Status'];

//     //             $this->setHeaders($event->sheet, $this->headers);

//     //             $startingRow = 2;
//     //             $count = 1;


//     //             foreach ($this->collection() as $row) {
//     //                 dd($row->{'status'});
//     //                 // $rowData = [
//     //                 //     $count++,
//     //                 //     'User-Id' => $row->{'id'},
//     //                 //     'Name' => $row->{'full_name'},
//     //                 //     'Email' => $row->{'email'},
//     //                 //     'Phone Number' => $row->{'phone_number'},
//     //                 //     'Postcode' => $row->{'postcode'},
//     //                 //     'Country' => $row->{'country'},
//     //                 //     'Status' => $row->{'status'},
//     //                 // ];

//     //                 // $event->sheet->append($rowData);
//     //             }

//     //         },
//     //     ];
//     // }

//     public function registerEvents(): array
//     {
//         return [
//             AfterSheet::class => function (AfterSheet $event) {
//                 $sheet = $event->sheet->getDelegate(); // Get the sheet
//                 $currentRow = 1;

//                 // Set headers
//                 $columnIndex = 'A';
//                 foreach ($this->headers as $header) {
//                     $sheet->setCellValue($columnIndex . $currentRow, $header);
//                     $columnIndex++;
//                 }

//                 // Set data
//                 $users = $this->collection(); // Fetch data
//                 dd($users);
//                 foreach ($users as $index => $user) {
//                     $currentRow++;
//                     $sheet->setCellValue("A$currentRow", $index + 1); // S.No
//                     $sheet->setCellValue("B$currentRow", $user->id);
//                     $sheet->setCellValue("C$currentRow", $user->full_name);
//                     $sheet->setCellValue("D$currentRow", $user->email);
//                     $sheet->setCellValue("E$currentRow", $user->phone_number);
//                     $sheet->setCellValue("F$currentRow", $user->postcode);
//                     $sheet->setCellValue("G$currentRow", $user->country);
//                     $sheet->setCellValue("H$currentRow", $user->status);
//                 }
//             },
//         ];
//     }
// }


class UsersExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    // Define the data to export
    public function array(): array
    {
        $users = User::whereBetween('created_at', [$this->startDate, $this->endDate])->get();

        $data = [];
        foreach ($users as $index => $user) {
            $data[] = [
                'S.No' => $index + 1,
                'User-Id' => $user->id,
                'Name' => $user->full_name,
                'Email' => $user->email,
                'Phone Number' => $user->phone_number,
                'Postcode' => $user->postcode,
                'Country' => $user->country,
                'Status' => $user->status,
            ];
        }

        //dd($data);

        return $data;
    }

    // Define the headers
    public function headings(): array
    {
        return [
            'S.No',
            'User-Id',
            'Name',
            'Email',
            'Phone Number',
            'Postcode',
            'Country',
            'Status',
        ];
    }

    // Apply styling using events
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:H1')->getFont()->setBold(true);
                $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal('center');
            },
        ];
    }
}

