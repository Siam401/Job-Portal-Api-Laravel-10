<?php

namespace Modules\JobApplication\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\JobApplication\Models\ApplicationStage;

class JobStageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ApplicationStage::truncate();
        ApplicationStage::insert(
            $this->dataSource()
        );
    }

    private function dataSource():array {
        return [
            [
                'id' => 1,
                'name' => 'Applicants',
                'slug' => 'applied'
            ],
            [
                'id' => 2,
                'name' => 'Short Listed',
                'slug' => 'shortlist'
            ],
            [
                'id' => 3,
                'name' => 'Interviewed',
                'slug' => 'interview'
            ],
            [
                'id' => 4,
                'name' => 'Rejected',
                'slug' => 'rejected'
            ],
            [
                'id' => 5,
                'name' => 'Waiting List',
                'slug' => 'waiting'
            ],
            [
                'id' => 6,
                'name' => 'Final Hiring List',
                'slug' => 'hired'
            ],
        ];

    }
}
