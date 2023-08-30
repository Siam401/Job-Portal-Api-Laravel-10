<?php

namespace Modules\Frontend\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Frontend\Models\SectionItem;

class SectionItemTableSeeder extends Seeder
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
            $sectionItems = json_decode(
                file_get_contents(__DIR__ . '/data/section_item.json')
            );

            if (count($sectionItems) > 0) {

                SectionItem::truncate();

                $insertArray = [];
                foreach ($sectionItems as $value) {
                    $section_id = $value->section_id;
                    $items = $value->items;
                    foreach ($items as $key => $item) {
                        $insertArray[] = [
                            'section_id' =>   $section_id,
                            'serial' =>   $key + 1,
                            'items' =>   json_encode($item)
                        ];
                    }
                }

                SectionItem::insert($insertArray);
            }
            return true;
        } catch (\Throwable $e) {
            $this->command->error($e->getMessage());
            return false;
        }
    }
}