<?php

namespace Modules\Job\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Company\Models\Company;
use Modules\Company\Services\JobCompanyService;
use Modules\Job\Models\Job;
use Modules\Job\Models\JobFunction;
use Modules\Location\Models\Division;
use Modules\Location\Services\DivisionService;
use Modules\Settings\Models\Setting;

class FrontendService
{

    public function jobCountByWings()
    {
        return Company::select('companies.code', 'companies.name', 'companies.logo', DB::raw('count(jobs.id) as job_count'))
            ->leftJoin('jobs', function ($join) {
                $join->on('jobs.wing_id', '=', 'companies.id')
                    ->where('jobs.status', '=', 1);
            })
            ->where('companies.level', Company::LEVEL_WING)
            ->groupBy('companies.id')
            ->get();

    }

    public function jobCountByCities()
    {
        return Division::select('divisions.id', 'divisions.name', DB::raw("'' AS logo"), DB::raw('count(jobs.id) AS job_count'))
            ->leftJoin('districts', 'districts.division_id', '=', 'divisions.id')
            ->leftJoin('companies', 'companies.district_id', '=', 'districts.id')
            ->leftJoin('jobs', function ($join) {
                $join->on('jobs.wing_id', '=', 'companies.id')
                    ->where('jobs.status', '=', 1);
            })
            ->groupBy('districts.division_id')
            ->get();

    }

    /**
     * Get job categories as array options
     *
     * @return array
     */
    public function categoryOptions(): array
    {
        $options = [];

        foreach (JobFunction::getCategories() as $category) {
            if (isTrue($category->is_active)) {
                $options[] = [
                    'value' => $category->id,
                    'text' => $category->name
                ];
            }
        }

        return $options;

    }

    public function jobDetail(string $code)
    {
        return Job::where('code', $code)->with('detail', 'jobFunction', 'wing', 'branch')->first();
    }

    public function getJobsWithPagination(Request $request)
    {
        $jobs = Job::select('code', 'title', 'job_functions.name as job_category', 'vacancy', 'end_date')
            ->leftJoin('job_functions', 'job_functions.id', '=', 'jobs.job_function_id');

        return $this->prepareFilters($request, $jobs)
            ->paginate($request->paginate_count ?? 10)
            ->appends(request()->input());
    }

    protected function prepareFilters(Request $request, Builder $query)
    {
        if ($request->search) {
            $query = $query->where('jobs.title', 'like', '%' . $request->search . '%');
        }

        if ($request->category) {
            $query = $query->whereIn('job_function_id', explode(',', $request->category));
        }

        if ($request->location) {
            $query = $query->where(function ($x) use ($request) {
                $companyIds = JobCompanyService::getCompanyIdsByLocation($request->location);

                $x->whereIn('wing_id', $companyIds)->orWhere(function ($y) use ($companyIds) {
                    $y->whereIn('branch_id', $companyIds);
                });
            });
        }

        if (Setting::isGroupCompany() && $request->company) {
            $query = $query->whereIn('wing_id', JobCompanyService::getCompanyIds($request->company));
        }

        return $query->active()
            ->orderBy('jobs.id', $request->sort && $request->sort == 'asc' ? 'asc' : 'desc');

    }
}