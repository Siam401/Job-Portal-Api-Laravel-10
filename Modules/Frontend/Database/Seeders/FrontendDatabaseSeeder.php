<?php

namespace Modules\Frontend\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class FrontendDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(SectionTableSeeder::class);
        $this->call(SocialLinkTableSeeder::class);
        $this->call(EducationTableSeeder::class);
        $this->call(SpecialSkillTableSeeder::class);

        if (config('app.test_mode')) {

            $this->command->info('Test Mode: Section Items & Image Assets Seeder are Running');
            $this->call(SectionItemTableSeeder::class);
            $this->assetFileSeeder();
        }

    }

    protected function assetFileSeeder()
    {
        if (is_dir(public_path('uploads/frontend'))) {
            rmdirRecursive(public_path('uploads/frontend'));
        }

        if (file_exists(__DIR__ . '/assets/frontend.zip')) {
            try {

                $zip = new \ZipArchive;
                $res = $zip->open(__DIR__ . '/assets/frontend.zip');
                if ($res === TRUE) {
                    $zip->extractTo(public_path('uploads/frontend'));
                    $zip->close();
                }
            } catch (\Throwable $th) {
                $this->command->info($th->getMessage());
            }
        }
    }
}