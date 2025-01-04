<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersCsvExport implements FromCollection, WithHeadings
{
    protected $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function collection()
    {
        // if ($this->users->isEmpty()) {
        //     dd('No users found in the collection.');
        // }

        //dd($this->users);

        return $this->users->map(function ($user) {
            return [
                $user->id,
                $user->full_name,
                $user->email,
                $user->phone_number,
                $user->postcode,
                $user->country,
                $user->status,
            ];
        });
    }

    // Define the headings for the CSV file
    public function headings(): array
    {
        return [
            'ID',
            'Full Name',
            'Email',
            'Phone Number',
            'Postcode',
            'Country',
            'Status',
        ];
    }
}

