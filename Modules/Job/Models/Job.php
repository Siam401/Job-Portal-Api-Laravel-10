<?php

namespace Modules\Job\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Modules\Company\Models\Company;
use Modules\JobApplication\Models\JobApplication;
use Modules\Settings\Models\Setting;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'title',
        'wing_id',
        'branch_id',
        'job_function_id',
        'vacancy',
        'code',
        'status',
        'start_date',
        'end_date'
    ];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_EXPIRED = 2;
    const STATUS_CLOSED = 3;

    /**
     * Get concatenated job titles
     *
     * @param string $companyCode
     * @return string
     */
    public static function getKeywords(string $companyCode):string
    {
        return Cache::remember('job_keywords_' . $companyCode, 60 * 60 * 24, function () {
            if (Setting::isGroupCompany()) {
                return Company::select('jobs.title as job_title')
                    ->leftJoin('jobs', 'jobs.wing_id', '=', 'companies.id')
                    ->groupBy('jobs.title')
                    ->get()->map(function ($company) {
                        return $company->job_title;
                    })->implode(',');

            } else {
                return Job::distinct('title')->groupBy('title')->get()->map(function ($job) {
                    return $job->title;
                })->implode(',');
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)->where('end_date', '>=', now());
    }

    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }
    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }
    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED)->orWhere('end_date', '<', now());
    }


    public function metas()
    {
        return $this->hasMany(JobMeta::class);
    }

    public function detail()
    {
        return $this->hasOne(JobDetail::class);
    }

    public function jobFunction()
    {
        return $this->belongsTo(JobFunction::class, 'job_function_id', 'id');
    }

    public function wing()
    {
        return $this->belongsTo(Company::class, 'wing_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Company::class, 'branch_id', 'id');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }
}