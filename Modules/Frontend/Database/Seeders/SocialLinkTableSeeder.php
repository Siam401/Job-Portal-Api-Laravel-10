<?php

namespace Modules\Frontend\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Frontend\Models\SocialLink;

class SocialLinkTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        try {
            # code...
            $socialLinks = json_decode(
                file_get_contents(__DIR__ . '/data/social_link.json')
            );

            if (count($socialLinks) > 0) {

                SocialLink::truncate();

                $insertArray = [];
                foreach ($socialLinks as $value) {
                    $insertArray[] = [
                        'title' =>   $value->title,
                        'url' =>   $value->url,
                        'icon_image' =>   $value->icon_image,
                        'icon_type' =>   $value->icon_type,
                        'serial' =>   $value->serial,
                        'is_active' =>   $value->is_active
                    ];
                }

                SocialLink::insert($insertArray);
            }
            return true;
        } catch (\Throwable $e) {
            $this->command->error($e->getMessage());
            return false;
        }
    }
}