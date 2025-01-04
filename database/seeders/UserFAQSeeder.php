<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserFAQSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_faqs')->insert([
            [
                'question' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit ?',
                'answer' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Adipisci magni quisquam repellat odit error. Totam esse placeat at officiis ullam, commodi ab provident ut. Doloribus incidunt officiis nisi nostrum fugiat.',
            ],
            [
                'question' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit ?',
                'answer' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Adipisci magni quisquam repellat odit error. Totam esse placeat at officiis ullam, commodi ab provident ut. Doloribus incidunt officiis nisi nostrum fugiat.',
            ],
            [
                'question' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit ?',
                'answer' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Adipisci magni quisquam repellat odit error. Totam esse placeat at officiis ullam, commodi ab provident ut. Doloribus incidunt officiis nisi nostrum fugiat.',
            ],
            [
                'question' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit ?',
                'answer' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Adipisci magni quisquam repellat odit error. Totam esse placeat at officiis ullam, commodi ab provident ut. Doloribus incidunt officiis nisi nostrum fugiat.',
            ],
            [
                'question' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit ?',
                'answer' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Adipisci magni quisquam repellat odit error. Totam esse placeat at officiis ullam, commodi ab provident ut. Doloribus incidunt officiis nisi nostrum fugiat.',
            ],

        ]);

    }
}
