<?php

namespace Modules\Company\Services;

use App\Services\FileUpload\FileUpload;
use Modules\Company\Models\Company;
use Modules\Job\Models\Job;
use Modules\Settings\Models\Setting;
use Modules\Settings\Services\ThemeService;

class JobCompanyService
{
    public static function getSummary(Company $company = null, Company $wing = null, Company $branch = null)
    {
        if (empty($company)) {
            $company = Company::first();
        }

        $weekends = $wing ? $wing->weekends : ($branch ? $branch->weekends : $company->weekends);

        return [
            'company_name' => $company->name,
            'company_code' => $company->code,
            'company_logo' => FileUpload::getUrl($company->logo ?? (new ThemeService)->siteLogo()),
            'wing_name' => $wing ? $wing->name : null,
            'wing_code' => $wing?->code ?? null,
            'wing_logo' => $wing && $wing->logo ? FileUpload::getUrl($wing->logo) : null,
            'address' => $company->address,
            'website' => $company->website,
            'office_hours' => date('h:i A', strtotime($company->office_start_time)) . ' - ' . date('h:i A', strtotime($company->office_end_time)),
            'weekends' => $weekends ? implode(',', array_map(function ($val) {
                $day = intval($val);
                return days()[$day > 0 ? $day : 1];
            }, is_string($weekends) ? explode(',', $weekends) : $weekends)) : '',
            'active_jobs' => self::activeJobs(
                Setting::isGroupCompany() ? 'group' : (Setting::isBranchEnabled() ? 'branch' : 'default')
                , $wing->id ?? $branch->id ?? $company->id)
        ];

    }

    /**
     * Return active job count
     *
     * @param string $type
     * @param integer $id
     * @return void
     */
    public static function activeJobs(string $type = 'group', int $id = 1)
    {
        return match ($type) {
            'group' => Job::active()->where('wing_id', $id)->count(),
            'branch' => Job::active()->where('branch_id', $id)->count(),
            default => Job::active()->count(),
        };
    }

    public static function getCompanyIds(string $companyCodes)
    {
        return Company::whereIn('code', explode(',', $companyCodes))->pluck('id')->toArray();
    }

    public static function getCompanyIdsByLocation(string $location)
    {
        return Company::select('companies.id')
            ->leftJoin('districts', 'districts.id', '=', 'companies.district_id')
            ->whereIn('districts.division_id', explode(',', $location))->pluck('id')->toArray();
    }
}