<?php

namespace Modules\Frontend\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Modules\Frontend\Models\Education;

class EducationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return bool
     */
    public function run()
    {
        Model::unguard();

        try {
            # code...
            $educations = json_decode(
                file_get_contents(__DIR__ . '/data/education.json')
            );

            if (count($educations) > 0) {

                Schema::disableForeignKeyConstraints();

                Education::truncate();

                $insertArray = [];
                foreach ($educations as $value) {
                    $insertArray[] = [
                        'id' => $value->id,
                        'name' => $value->name,
                        'serial' => $value->serial,
                        'is_active' => 1
                    ];
                }

                Education::insert($insertArray);

                Schema::enableForeignKeyConstraints();
            }
            return true;
        } catch (\Throwable $e) {
            $this->command->error($e->getMessage());
            return false;
        }
    }
}