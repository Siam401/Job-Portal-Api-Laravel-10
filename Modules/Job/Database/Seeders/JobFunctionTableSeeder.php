<?php

namespace Modules\Job\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Job\Models\JobFunction;
use Illuminate\Support\Str;

class JobFunctionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $categories = [
            'Account / Finance',
            'Architect',
            'Banking / Financial Institution',
            'Commercial',
            'Construction',
            'Design / Creative',
            'Education / Training',
            'Engineering / Architect',
            'Garments / Textile',
            'Hospitality / Travel / Tourism',
            'Housing Apartment',
            'HR / Admin',
            'IT & Telecommunication',
            'Production / Operation',
            'Restaurants',
            'Supply Chain / Procurement',
        ];

        JobFunction::truncate();

        foreach ($categories as $value) {
            JobFunction::create([
                'name' => $value,
                'slug' => Str::slug($value)
            ]);
        }
    }
}