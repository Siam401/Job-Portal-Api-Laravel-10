<?php

namespace Modules\Frontend\Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Modules\Frontend\Database\Seeders\SectionItemTableSeeder;
use Modules\Frontend\Database\Seeders\SectionTableSeeder;
use Modules\Frontend\Models\Section;
use Modules\Frontend\Models\SectionItem;
use Tests\TestCase;

class FrontendTest extends TestCase
{
    public function test_if_layout_api_return_success()
    {
        $response = $this->getJson('/api/frontend/get-layouts');

        $response->assertStatus(200);
    }

    public function test_if_section_api_return_success()
    {
        $sections = Section::all();

        foreach ($sections as $row) {
            $response = $this->getJson('/api/frontend/section/' . $row->slug);

            $response->assertStatus(200);
        }
    }

    public function test_layout_api_if_empty()
    {
        $response = $this->getJson('/api/frontend/section/asd');

        $response->assertStatus(404);
    }

    public function test_job_wing_api_success()
    {
        $sectionSeeder = new SectionTableSeeder;
        $sectionItemSeeder = new SectionItemTableSeeder;
        $sectionSeeder->run();
        $sectionItemSeeder->run();

        $response = $this->getJson('/api/frontend/job-wings');

        $response->assertStatus(200);
    }

    public function test_job_wing_api_failed()
    {
        Schema::disableForeignKeyConstraints();
        SectionItem::truncate();
        Section::truncate();
        Schema::enableForeignKeyConstraints();

        $response = $this->getJson('/api/frontend/job-wings');

        $response->assertStatus(404);

        $sectionSeeder = new SectionTableSeeder;
        $sectionItemSeeder = new SectionItemTableSeeder;
        $sectionSeeder->run();
        $sectionItemSeeder->run();
    }

    public function test_job_cities_api_success()
    {
        $sectionSeeder = new SectionTableSeeder;
        $sectionItemSeeder = new SectionItemTableSeeder;
        $sectionSeeder->run();
        $sectionItemSeeder->run();

        $response = $this->getJson('/api/frontend/job-cities');

        $response->assertStatus(200);
    }

    public function test_job_cities_api_failed()
    {
        Schema::disableForeignKeyConstraints();
        SectionItem::truncate();
        Section::truncate();
        Schema::enableForeignKeyConstraints();

        $response = $this->getJson('/api/frontend/job-cities');

        $response->assertStatus(404);

        $sectionSeeder = new SectionTableSeeder;
        $sectionItemSeeder = new SectionItemTableSeeder;
        $sectionSeeder->run();
        $sectionItemSeeder->run();
    }

    public function test_job_function_api_success()
    {
        $response = $this->getJson('/api/frontend/job-functions');

        $response->assertStatus(200);
    }

    public function test_job_skills_api_success()
    {
        $response = $this->getJson('/api/frontend/special-skills');

        $response->assertStatus(200);
    }

    public function test_educations_api_success()
    {
        $response = $this->getJson('/api/frontend/educations');

        $response->assertStatus(200);
    }
}
