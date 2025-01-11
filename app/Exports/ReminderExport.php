<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Reminder;


class ReminderExport implements FromCollection, WithHeadings, WithEvents
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return Reminder::with('user:id,full_name')
            ->whereBetween('created_at', [
                $this->startDate->startOfDay(),
                $this->endDate->endOfDay(),
            ])
            ->get()
            ->map(function ($reminder) {
                return [
                    'id' => $reminder->id,
                    'user_name' => $reminder->user->full_name,
                    'title' => $reminder->title,
                    'category' => $reminder->category,
                    'subcategory' => $reminder->subcategory,
                    'due_date' => $reminder->due_date,
                    'time' => $reminder->time,
                    'provider' => $reminder->provider,
                    'cost' => $reminder->cost,
                    'description' => $reminder->description,
                    'payment_frequency' => $reminder->payment_frequency,
                    'status' => $reminder->reminder_status,
                ];
            });
    }


    public function headings(): array
    {
        return [
            ['Reminder Report'],
            ['Reminder Id', 'User Name', 'Title', 'Category', 'Sug Category', 'Due Date', 'Time', 'Provider', 'Cost', 'Description', 'Payment Frequency', 'Status'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $sheet->mergeCells('A1:L1');

                $sheet->getStyle('A1:L1')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'FFFF00',
                        ],
                    ],
                ]);
    
                $sheet->getRowDimension(1)->setRowHeight(30);

                $sheet->getStyle('A2:L2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $sheet->getStyle('A3:L' . ($sheet->getHighestRow()))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                    ],
                ]);

                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(40);
                $sheet->getColumnDimension('D')->setWidth(30);
                $sheet->getColumnDimension('E')->setWidth(25);
                $sheet->getColumnDimension('F')->setWidth(20);
                $sheet->getColumnDimension('G')->setWidth(20);
                $sheet->getColumnDimension('H')->setWidth(25);
                $sheet->getColumnDimension('I')->setWidth(20);
                $sheet->getColumnDimension('J')->setWidth(40);
                $sheet->getColumnDimension('K')->setWidth(20);
                $sheet->getColumnDimension('L')->setWidth(20);

                $sheet->getStyle('J')->getAlignment()->setWrapText(true);
            }
        ];
    }
}