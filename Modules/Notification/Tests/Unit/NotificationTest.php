<?php

namespace Modules\Notification\Tests\Unit;

use Tests\TestCase;

class NotificationTest extends TestCase
{
    public function test_if_notification_api_return_success()
    {
        $response = $this->getJson('/api/applicant/notifications');

        $response->assertStatus(200);
    }


    public function test_if_get_configurations_api_return_success()
    {
        $response = $this->getJson('/api/configurations');

        $response->assertStatus(200);
    }
}
