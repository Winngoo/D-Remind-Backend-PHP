<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\News;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        News::create([
            'title' => 'Breaking News',
            'description' => 'This is a sample news description.',
            'date' => now()->toDateString(),
            'time' => now()->toTimeString(),
        ]); 
    }
}
