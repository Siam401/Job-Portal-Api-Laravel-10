<?php

namespace Modules\Job\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Job\Models\Job;

class JobTest extends TestCase
{
    public function test_job_category_api_success()
    {
        $response = $this->getJson('/api/job/get-categories');

        $response->assertStatus(200);
    }

    public function test_job_list_api_success()
    {

        $response = $this->getJson('/api/job/list');

        $response->assertStatus(200);
    }

    public function test_job_detail_api_success()
    {
        $job = Job::where('status', 1)->first();
        $response = $this->getJson('/api/job/detail/' . $job->code);

        $response->assertStatus(200);
    }

    public function test_if_job_not_active()
    {
        $job = Job::first();
        $job->status = 0;
        $job->save();

        $response = $this->getJson('/api/job/detail/' . $job->code);

        $response->assertStatus(400);

        $job->status = 1;
        $job->save();
    }

    public function test_if_job_expired()
    {
        $job = Job::first();
        $job->end_Date = '1996-08-31';
        $job->save();

        $response = $this->getJson('/api/job/detail/' . $job->code);

        $response->assertStatus(400);

        $job->end_Date = '2023-08-31';
        $job->save();
    }

    public function test_job_detail_api_failed()
    {
        $response = $this->getJson('/api/job/detail');

        $response->assertStatus(404);
    }
}
