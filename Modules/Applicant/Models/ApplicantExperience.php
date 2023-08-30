<?php

namespace Modules\Applicant\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;

class ApplicantExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_business',
        'designation',
        'location',
        'start_date',
        'end_date',
        'is_current',
        'responsibility',
        'expertise',
    ];

    protected $casts = [
        'expertise' => 'array',
        'is_current' => 'boolean',
    ];

    public static function prepareResponse(array|Collection $experiences)
    {
        $data = [];
        $totalExperience = 0;

        foreach ($experiences as $experience) {
            $years = Carbon::parse($experience->start_date)->diffInYears($experience->end_date ?? now());
            $data[] = [
                'designation' => $experience->designation,
                'total_years' => $years,
                'start_month' => Carbon::parse($experience->start_date)->format('F, Y'),
                'end_month' => $experience->is_current ? null : Carbon::parse($experience->end_date)->format('F, Y'),
                'company_name' => $experience->company_name,
                // 'company_business' => $experience->company_business,
                'company_address' => $experience->location,
                'responsibility' => $experience->responsibility,
                'expertise' => is_array($experience->expertise) ? array_reduce($experience->expertise, function($carry, $item) {
                    $year = floatval($item['year'] . '.' . floor($item['month'] * 10/12));
                    $carry .= (!empty($carry) ? ', ' : '') . $item['name'] . ' ('. $year .' yrs)';
                    return $carry;
                }, '') : ''
            ];

            $totalExperience += $years;
        }

        return [
            'total_experience' => $totalExperience,
            'experiences' => $data,
        ];
    }
}
