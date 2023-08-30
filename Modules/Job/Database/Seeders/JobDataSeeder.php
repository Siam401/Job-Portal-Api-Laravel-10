<?php

namespace Modules\Job\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Job\Models\Job;
use Modules\Job\Models\JobDetail;
use Modules\Job\Models\JobFunction;
use Modules\Job\Models\JobMeta;
use Illuminate\Support\Facades\Schema;

class JobDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return bool
     */
    public function run()
    {


        try {
            # code...
            $jobData = json_decode(
                file_get_contents(__DIR__ . '/data/job.json')
            );

            if (count($jobData) > 0) {

                Schema::disableForeignKeyConstraints();
                JobDetail::truncate();
                JobMeta::truncate();
                Job::truncate();
                Schema::enableForeignKeyConstraints();

                $job = $jobDetail = null;

                foreach ($jobData as $data) {
                    if ($data->table === 'jobs') {

                        $job = Job::create((array) $data->rows[0]);
                    } elseif ($data->table === 'job_details') {

                        $jobDetail = $this->addJobDetail($data->rows[0]);
                    }
                }

                if ($job && $jobDetail) {
                    $job = $job->toArray();
                    for ($i = ($job['id'] + 1); $i < 23; $i++) {
                        $this->addJob($job, $i);
                    }
                }
            }
            return true;
        } catch (\Throwable $e) {
            $this->command->error($e->getMessage() . ' Line:' . $e->getLine() . ' Class:' . self::class);
            return false;
        }
    }

    private function addJob(object|array $data, int $id)
    {
        unset($data['id']);
        $data['id'] = $id;
        if ($id % 2) {
            $data['wing_id'] = 2;
            $data['branch_id'] = 3;
        } else {
            $data['wing_id'] = 4;
            $data['branch_id'] = 5;
        }
        $data['code'] = date('Ymd') . rand(1000, 9999);

        $job = Job::create($data);

        $jobDetail = JobDetail::first()->toArray();
        unset($jobDetail['id']);
        $jobDetail['job_id'] = $job->id;
        $jobDetail['form_visibility'] = implode(',', $jobDetail['form_visibility'] ?? []);
        $jobDetail['questions'] = implode(',', $jobDetail['questions'] ?? []);
        $jobDetail = JobDetail::create($jobDetail);

        return;


        // return Job::create([
        //     'category' => $rows->category,
        //     'title' => $rows->title,
        //     'wing_id' => $rows->wing_id,
        //     'job_function_id' => $rows->job_function_id,
        //     'branch_id' => $rows->branch_id,
        //     'vacancy' => $rows->vacancy,
        //     'code' => $rows->code,
        //     'status' => $rows->status,
        //     'start_date' => $rows->start_date,
        //     'end_date' => $rows->end_date,

        // ]);
    }

    private function addJobDetail(object $rows)
    {
        return JobDetail::create([
            'job_id' => $rows->job_id,
            'job_type' => $rows->job_type,
            'workplace' => $rows->workplace,
            'salary' => $rows->salary,
            'gender' => $rows->gender,
            'form_visibility' => $rows->form_visibility,
            'questions' => $rows->questions,
            'skills' => $rows->skills,
            'description' => $rows->description,
            'responsibility' => $rows->responsibility,
            'education' => $rows->education,
            'experience' => $rows->experience,
            'benefit' => $rows->benefit,
            'additional' => $rows->additional,
            'age_min' => $rows->age_min,
            'age_max' => $rows->age_max,
            'min_exp' => $rows->min_exp,
            'max_exp' => $rows->max_exp,

        ]);

    }
}