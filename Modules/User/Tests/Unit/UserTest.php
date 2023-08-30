<?php

namespace Modules\User\Tests\Unit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Modules\User\Models\Applicant;
use Modules\User\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_user_registration_success()
    {
        $dataSet = $this->preparedData();

        $response = $this->post('/api/auth/register', $dataSet);

        $response->assertStatus(200);

        $this->deletedData();
    }

    public function test_user_login_success()
    {
        $dataSet = $this->preparedData();

        $this->post('/api/auth/register', $dataSet);

        $userCredential = [
            'username' => 'test@example.com',
            'password' => '123123'
        ];

        $response = $this->post('/api/auth/login', $userCredential);

        $response->assertStatus(200);

        $this->deletedData();
    }

    public function test_user_login_failed()
    {
        $userCredential = [
            'username' => 'dummy@example.com',
            'password' => '123123'
        ];

        $response = $this->post('/api/auth/login', $userCredential);

        $response->assertStatus(401);
    }

    private function preparedData()
    {
        Storage::fake('avatars');
        Storage::fake('documents');

        $photo = UploadedFile::fake()->image('avatar.jpg');
        $file = UploadedFile::fake()->create('documents.pdf');

        $dataSet = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'country_code' => '880',
            'mobile' => '01710222333',
            'gender' => 'male',
            'photo' => $photo,
            'resume' => $file,
            'password' => '123123',
            'confirm_password' => '123123',
        ];

        return $dataSet;
    }

    private function deletedData()
    {
        Schema::disableForeignKeyConstraints();
        User::where('email', 'test@example.com')->delete();
        Applicant::where('email', 'test@example.com')->delete();
        Schema::enableForeignKeyConstraints();

        return true;
    }
}
