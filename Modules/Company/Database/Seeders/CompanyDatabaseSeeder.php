<?php

namespace Modules\Company\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Company\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Modules\Settings\Models\Setting;

class CompanyDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Schema::disableForeignKeyConstraints();

        Company::truncate();

        $name = 'Next Group';
        $company = Company::create([
            'name' => $name,
            'level' => Company::LEVEL_COMPANY,
            'code' => strtoupper(Str::slug($name)),
            'email' => 'company@example.com',
            'address' => 'Dhaka, Bangladesh',
            'district_id' => 47,
            'weekends' => [6, 7],
            'website' => 'https://nextitltd.com/',
        ]);

        if ($company->id && config('app.test_mode')) {
            $this->addWingsBranches($company);
        }

        Schema::enableForeignKeyConstraints();
    }

    private function addWingsBranches(Company $company)
    {
        if (!Setting::isGroupCompany()) {
            Company::create([
                'name' => $company->name,
                'level' => Company::LEVEL_WING,
                'code' => $company->slug . '-wing',
                'email' => $company->email,
                'parent_id' => $company->id,
                'address' => $company->address,
                'district_id' => $company->district_id
            ]);
            return;
        }

        $this->command->info('Test Mode: Wings and Branches Seeder is Running');
        $name = 'Winbees';
        $wing = Company::create([
            'name' => $name,
            'level' => Company::LEVEL_WING,
            'code' => strtoupper(Str::slug($name)),
            'email' => 'winbees@example.com',
            'parent_id' => $company->id,
            'address' => 'Dhaka, Bangladesh',
            'district_id' => 47,
            'weekends' => [6, 7],
            'website' => 'https://nextitltd.com/',
        ]);

        if (Setting::isBranchEnabled()) {
            $name = 'Khilgaon';
            $branch = Company::create([
                'name' => $name,
                'level' => Company::LEVEL_BRANCH,
                'code' => strtoupper(Str::slug($name)),
                'email' => 'winbees-khilgaon@example.com',
                'address' => 'Dhaka, Bangladesh',
                'parent_id' => $wing->id,
                'district_id' => 47,
                'weekends' => [6, 7],
                'website' => 'https://nextitltd.com/',
            ]);
        }

        $name = 'Next IT';
        $wing = Company::create([
            'name' => $name,
            'level' => Company::LEVEL_WING,
            'code' => strtoupper(Str::slug($name)),
            'email' => 'nextit@example.com',
            'address' => 'Dhaka, Bangladesh',
            'parent_id' => $company->id,
            'district_id' => 27,
            'weekends' => [6, 7],
            'website' => 'https://nextitltd.com/',
        ]);

        if (Setting::isBranchEnabled()) {
            $name = 'Banani';
            $branch = Company::create([
                'name' => $name,
                'level' => Company::LEVEL_BRANCH,
                'code' => strtoupper(Str::slug($name)),
                'email' => 'nextit-banani@example.com',
                'address' => 'Dhaka, Bangladesh',
                'parent_id' => $wing->id,
                'district_id' => 27,
                'weekends' => [6, 7],
                'website' => 'https://nextitltd.com/',
            ]);
        }

        return;
    }
}