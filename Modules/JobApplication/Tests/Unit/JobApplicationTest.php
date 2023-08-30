<?php

namespace Modules\JobApplication\Tests\Unit;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Modules\Job\Models\Job;
use Modules\JobApplication\Models\JobApplication;
use Modules\User\Models\Applicant;

class JobApplicationTest extends TestCase
{
    public function test_job_apply_success()
    {
        $job = Job::where('status', 1)->where('end_date', '>', Carbon::now())->first();

        $dataSet = $this->preparedData($job->code);

        $response = $this->post('/api/job/apply', $dataSet);

        $response->assertStatus(200);

        $this->deletedData($job->id);
    }

    public function test_if_job_not_active()
    {
        $job = Job::first();
        $job->status = 0;
        $job->save();

        $dataSet = $this->preparedData($job->code);

        $response = $this->post('/api/job/apply', $dataSet);

        $response->assertStatus(200);

        $job->status = 1;
        $job->save();

        $this->deletedData($job->id);
    }

    public function test_if_job_expired()
    {
        $job = Job::first();
        $job->end_Date = '1996-08-31';
        $job->save();

        $dataSet = $this->preparedData($job->code);

        $response = $this->post('/api/job/apply', $dataSet);

        $response->assertStatus(400);

        $this->deletedData($job->id);
        $job->end_Date = '2023-08-31';
        $job->save();

        $this->deletedData($job->id);
    }

    private function preparedData($jobCode)
    {
        Storage::fake('avatars');
        Storage::fake('documents');

        $photo = UploadedFile::fake()->image('avatar.jpg');
        $file = UploadedFile::fake()->create('documents.pdf');


        $questionArray = [[
            'question' => 'Can you work independently?',
            'answer' => 'Yes, I can'
        ], [
            'question' => 'Can you work independently?',
            'answer' => 'Yes, I can'
        ]];

        $dataSet = [
            'job_code' => intval($jobCode),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'country_code' => '880',
            'mobile' => '01710222333',
            'gender' => 'male',
            'photo' => $photo,
            'resume' => $file,
            'cover_letter' => 'This is test cover letter',
            'questions' => $questionArray,
        ];

        return $dataSet;
    }

    private function deletedData($jobId)
    {
        Schema::disableForeignKeyConstraints();
        JobApplication::where('job_id', $jobId)->delete();
        Applicant::where('email', 'test@example.com')->delete();
        Schema::enableForeignKeyConstraints();

        return true;
    }
}