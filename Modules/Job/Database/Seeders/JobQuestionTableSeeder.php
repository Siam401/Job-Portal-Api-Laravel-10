<?php

namespace Modules\Job\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Job\Models\JobQuestion;

class JobQuestionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $questions = [
            [
                'question' => 'Can you work independently?',
                'is_required' => false,
                'is_active' => true,
            ],
            [
                'question' => 'Do you have experience as Project Manager?',
                'is_required' => false,
                'is_active' => true,
            ],
        ];

        JobQuestion::truncate();

        JobQuestion::insert($questions);
    }
}
