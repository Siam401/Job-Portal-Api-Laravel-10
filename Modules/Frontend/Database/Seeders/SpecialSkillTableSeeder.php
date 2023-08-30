<?php

namespace Modules\Frontend\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Frontend\Models\SpecialSkill;

class SpecialSkillTableSeeder extends Seeder
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
            $skills = json_decode(
                file_get_contents(__DIR__ . '/data/special_skills.json')
            );

            if (count($skills) > 0) {

                SpecialSkill::truncate();

                $insertArray = [];
                foreach ($skills as $value) {
                    $insertArray[] = [
                        'name' => $value->name,
                        'name_bangla' => $value->name_bangla,
                        'is_active' => 1
                    ];
                }

                SpecialSkill::insert($insertArray);
            }
            return true;
        } catch (\Throwable $e) {
            $this->command->error($e->getMessage());
            return false;
        }
    }
}