<?php

namespace Modules\Frontend\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Modules\Frontend\Models\Section;

class SectionTableSeeder extends Seeder
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
            $sections = json_decode(
                file_get_contents(__DIR__ . '/data/section.json')
            );

            Schema::disableForeignKeyConstraints();
            if (count($sections) > 0) {

                Section::truncate();

                $insertArray = [];
                foreach ($sections as $value) {
                    $insertArray[] = [
                        'id' => $value->id,
                        'title' => $value->title,
                        'slug' => $value->slug,
                        'subtitle' => $value->subtitle,
                        'image' => $value->image != "" ? $value->image : null,
                        'description' => $value->description != "" ? $value->description : null,
                        'is_active' => $value->is_active,
                        'extra' => $value->extra != "" ? json_encode($value->extra) : null,
                    ];
                }

                Section::insert($insertArray);
            }
            Schema::enableForeignKeyConstraints();
            return true;
        } catch (\Throwable $e) {
            $this->command->error($e->getMessage());
            return false;
        }

    }
}