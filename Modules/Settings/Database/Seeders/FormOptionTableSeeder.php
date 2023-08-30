<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Settings\Models\FormOption;

class FormOptionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FormOption::truncate();

        $optionArray = $this->dataSource();

        $insertArray = [];

        foreach ($optionArray as $name => $options) {
            if (is_array($options) && count($options) > 0) {
                foreach ($options as $option) {
                    $option['name'] = $name;
                    $insertArray[] = $option;
                }
            }
        }

        FormOption::insert($insertArray);
    }

    private function dataSource()
    {
        return [
            'gender' => [
                [
                    'option_title' => 'Male',
                    'option_value' => 'male'
                ],
                [
                    'option_title' => 'Female',
                    'option_value' => 'female'
                ],
                [
                    'option_title' => 'Other',
                    'option_value' => 'other'
                ],
            ],

            'workplace' => [
                [
                    'option_title' => 'Work From Home',
                    'option_value' => 'wfh'
                ],
                [
                    'option_title' => 'Work in The Office',
                    'option_value' => 'office'
                ],
                [
                    'option_title' => 'Hybrid',
                    'option_value' => 'hybrid'
                ],

            ],
            'job_status' => [
                [
                    'option_title' => 'Inactive',
                    'option_value' => 0
                ],
                [
                    'option_title' => 'Active',
                    'option_value' => 1
                ],
                [
                    'option_title' => 'Expired',
                    'option_value' => 2
                ],
                [
                    'option_title' => 'Closed',
                    'option_value' => 3
                ],
            ],
            'job_type' => [
                [
                    'option_title' => 'Full Time',
                    'option_value' => 'full_time'
                ],
                [
                    'option_title' => 'Part Time',
                    'option_value' => 'part_time'
                ],
                [
                    'option_title' => 'Contractual',
                    'option_value' => 'contractual'
                ],
                [
                    'option_title' => 'Internship',
                    'option_value' => 'internship'
                ],
                [
                    'option_title' => 'Freelance',
                    'option_value' => 'freelance'
                ],
            ],
            'job_level' => [
                [
                    'option_title' => 'Entry Level',
                    'option_value' => 'entry'
                ],
                [
                    'option_title' => 'Mid Level',
                    'option_value' => 'mid'
                ],
                [
                    'option_title' => 'Top Level',
                    'option_value' => 'top'
                ],
            ],
            'marriage' => [
                [
                    'option_title' => 'Married',
                    'option_value' => 'married'
                ],
                [
                    'option_title' => 'Unmarried',
                    'option_value' => 'unmarried'
                ],
                [
                    'option_title' => 'Divorced',
                    'option_value' => 'divorced'
                ],
                [
                    'option_title' => 'Widowed',
                    'option_value' => 'widowed'
                ],
            ],
            'religion' => [
                [
                    'option_title' => 'Islam',
                    'option_value' => 'islam'
                ],
                [
                    'option_title' => 'Hinduism',
                    'option_value' => 'hinduism'
                ],
                [
                    'option_title' => 'Christianity',
                    'option_value' => 'christianity'
                ],
                [
                    'option_title' => 'Buddhism',
                    'option_value' => 'buddhism'
                ],
                [
                    'option_title' => 'Other',
                    'option_value' => 'other'
                ],
            ],
            'learn_by' => [
                [
                    'option_title' => 'Job',
                    'option_value' => 'job'
                ],
                [
                    'option_title' => 'Educational',
                    'option_value' => 'education'
                ],
                [
                    'option_title' => 'Professional Training',
                    'option_value' => 'profession'
                ],
                [
                    'option_title' => 'Self',
                    'option_value' => 'self'
                ],
            ]
        ];

    }
}