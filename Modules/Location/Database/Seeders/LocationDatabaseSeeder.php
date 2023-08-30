<?php

namespace Modules\Location\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Modules\Location\Models\Area;
use Modules\Location\Models\Country;
use Modules\Location\Models\District;
use Modules\Location\Models\Division;
use Illuminate\Support\Str;

class LocationDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->command->info('Invoking Location Seeder');

        Schema::disableForeignKeyConstraints();

        $this->addCountries();
        $this->addDivisions();
        $this->addDistricts();
        $this->addAreas();

        Schema::enableForeignKeyConstraints();

        // $this->call("OthersTableSeeder");
    }

    private function addCountries()
    {
        try {
            # code...
            $countries = json_decode(
                file_get_contents(__DIR__ . '/data/country.json')
            );

            if (count($countries) > 0) {

                Country::truncate();

                $insertArray = [];
                foreach ($countries as $value) {
                    $insertArray[] = [
                        'name' => trim($value->name),
                        'iso_code' => trim($value->iso_code),
                        'country_code' => trim($value->country_code),
                        'nationality' => $value->nationality ?? ''
                    ];
                }

                Country::insert($insertArray);
            }
            return true;
        } catch (\Throwable $e) {
            $this->command->error($e->getMessage());
            return false;
        }
    }

    private function addDivisions()
    {
        $country = Country::where(['iso_code' => 'BD'])->first();
        try {
            # code...
            $divisions = json_decode(
                file_get_contents(__DIR__ . '/data/division.json')
            );

            if (count($divisions) > 0) {
                $insertArray = [];
                Division::truncate();
                foreach ($divisions as $value) {
                    $insertArray[] = [
                        "id" => $value->id,
                        'country_id' => $country->id,
                        'name' => $value->name,
                        'slug' => Str::slug($value->name)
                    ];
                }

                Division::insert($insertArray);
            }
            return true;
        } catch (\Throwable $e) {
            $this->command->error($e->getMessage());
            return false;
        }
    }

    private function addDistricts()
    {
        try {
            # code...
            $districts = json_decode(
                file_get_contents(__DIR__ . '/data/district.json')
            );

            if (count($districts) > 0) {
                $insertArray = [];
                District::truncate();
                foreach ($districts as $value) {
                    $insertArray[] = [
                        "id" => $value->id,
                        "division_id" => $value->division_id,
                        "name" => $value->name,
                        "latitude" => $value->latitude,
                        "longitude" => $value->longitude,
                    ];
                }

                District::insert($insertArray);
            }
            return true;
        } catch (\Throwable $e) {
            $this->command->error($e->getMessage());
            return false;
        }
    }

    private function addAreas()
    {
        try {
            # code...
            $areas = json_decode(
                file_get_contents(__DIR__ . '/data/area.json')
            );

            if (count($areas) > 0) {
                $insertArray = [];
                Area::truncate();
                foreach ($areas as $value) {
                    $insertArray[] = [
                        "id" => $value->id,
                        "district_id" => $value->district_id,
                        "name" => $value->name,
                    ];
                }

                Area::insert($insertArray);
            }
            return true;
        } catch (\Throwable $e) {
            $this->command->error($e->getMessage());
            return false;
        }
    }
}