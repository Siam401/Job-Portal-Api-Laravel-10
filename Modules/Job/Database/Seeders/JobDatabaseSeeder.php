<?php

namespace Modules\Job\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class JobDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(JobFunctionTableSeeder::class);

        if (config('app.test_mode')) {
            $this->command->info('Test Mode: Job Questions & Jobs Seeder is Running');

            $this->call(JobQuestionTableSeeder::class);
            $this->call(JobDataSeeder::class);
        }
    }
}