<?php

namespace Modules\Company\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyTest extends TestCase
{
    public function test_company_wing_api_success()
    {
        $response = $this->getJson('/api/company/get-wings');

        $response->assertStatus(200);
    }
}
