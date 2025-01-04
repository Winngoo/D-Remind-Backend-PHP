<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TermsAndCondition;

class TermsConditionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TermsAndCondition::create([
            'content' => 'These are the initial Terms and Conditions for our platform. Please read them carefully.',
        ]);
    }
}
