<?php

namespace Modules\Settings\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingsTest extends TestCase
{
    public function test_if_form_option_api_return_success()
    {
        $response = $this->getJson('/api/settings/form-options');

        $response->assertStatus(200);
    }
}
