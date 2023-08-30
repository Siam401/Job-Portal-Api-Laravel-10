<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Settings\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::truncate();
        $insertArray = $this->dataSource();
        Setting::insert($insertArray);
    }

    private function dataSource()
    {
        return [
            [
                'name' => 'is_group_company',
                'value' => '1',
                'data_type' => 'boolean',
            ],
            [
                'name' => 'is_ngo',
                'value' => '1',
                'data_type' => 'boolean',
            ],
            [
                'name' => 'has_branch',
                'value' => '1',
                'data_type' => 'boolean',
            ],
            [
                'name' => 'site_title',
                'value' => 'Job Portal',
                'data_type' => 'string',
            ],
            [
                'name' => 'site_description',
                'value' => 'Job Portal',
                'data_type' => 'text',
            ],
            [
                'name' => 'site_logo',
                'value' => is_file(public_path('uploads/frontend/logo.png')) ? 'frontend/logo.png' : '',
                'data_type' => 'image',
            ],
            [
                'name' => 'site_favicon',
                'value' => '',
                'data_type' => 'image',
            ],
            [
                'name' => 'support_email',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'support_phone',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'support_hotline',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'footer_text',
                'value' => 'Next Job Portal © 2024 | All Rights Reserved',
                'data_type' => 'text',
            ],
            [
                'name' => 'footer_logo',
                'value' => '',
                'data_type' => 'image',
            ],
            [
                'name' => 'color_primary',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'color_secondary',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'color_success',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'color_warning',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'color_info',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'color_danger',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'maintenance_mode',
                'value' => '1',
                'data_type' => 'boolean',
            ],
            [
                'name' => 'maintenance_description',
                'value' => 'Site is under maintenance. Please come back later.',
                'data_type' => 'text',
            ],
            [
                'name' => 'force_ssl',
                'value' => '0',
                'data_type' => 'boolean',
            ],
            [
                'name' => 'seo_title',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'seo_description',
                'value' => '',
                'data_type' => 'text',
            ],
            [
                'name' => 'seo_keywords',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'seo_image',
                'value' => '',
                'data_type' => 'image',
            ],

            // Environment Data
            [
                'name' => 'mail_mailer',
                'value' => 'smtp',
                'data_type' => 'string',
            ],
            [
                'name' => 'mail_host',
                'value' => 'smtp.office365.com',
                'data_type' => 'string',
            ],
            [
                'name' => 'mail_port',
                'value' => '',
                'data_type' => 'integer',
            ],
            [
                'name' => 'mail_username',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'mail_password',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'mail_encryption',
                'value' => 'tls',
                'data_type' => 'string',
            ],
            [
                'name' => 'mail_from_name',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'mail_from_address',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'sms_ssl_api_token',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'sms_ssl_sid',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'sms_ssl_csms_id',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'sms_ssl_url',
                'value' => '',
                'data_type' => 'string',
            ],
            [
                'name' => 'demo_test',
                'value' => 0,
                'data_type' => 'boolean',
            ],
            [
                'name' => 'time_otp_resend',
                'value' => 5,
                'data_type' => 'integer',
            ],
            [
                'name' => 'time_otp_lifetime',
                'value' => 10,
                'data_type' => 'integer',
            ],
            [
                'name' => 'time_zone',
                'value' => "Asia/Dhaka",
                'data_type' => 'string',
            ],
            [
                'name' => 'frontend_url',
                'value' => "http://localhost:3000",
                'data_type' => 'url',
            ],
            [
                'name' => 'app_debug',
                'value' => 1,
                'data_type' => 'boolean',
            ],
        ];
    }
}