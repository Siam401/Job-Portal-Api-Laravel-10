<?php

namespace Modules\Location\Tests\Unit;

use Tests\TestCase;
use Modules\Location\Models\District;
use Modules\Location\Models\Division;

class LocationTest extends TestCase
{
    public function test_country_api_success()
    {
        $response = $this->getJson('/api/location/get-countries');

        $response->assertStatus(200);
    }

    public function test_country_option_api_success()
    {
        $response = $this->getJson('/api/location/get-countries/option');

        $response->assertStatus(200);
    }

    public function test_division_api_success()
    {
        $response = $this->getJson('/api/location/get-divisions');

        $response->assertStatus(200);
    }

    public function test_district_api_success()
    {
        $division = Division::first();

        $response = $this->getJson('/api/location/get-districts?division_id=' . $division->id);

        $response->assertStatus(200);
    }

    public function test_district_api_failed()
    {
        $response = $this->getJson('/api/location/get-districts');

        $response->assertStatus(422);
    }

    public function test_area_api_success()
    {
        $district = District::first();

        $response = $this->getJson('/api/location/get-areas?district_id=' . $district->id);

        $response->assertStatus(200);
    }

    public function test_area_api_failed()
    {
        $response = $this->getJson('/api/location/get-areas');

        $response->assertStatus(422);
    }

    public function test_timezone_api_success()
    {
        $response = $this->getJson('/api/location/get-timezones');

        $response->assertStatus(200);
    }
}
