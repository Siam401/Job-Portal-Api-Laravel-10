<?php

namespace App\Http\Controllers\Mock;

use App\Http\Controllers\Controller;
use App\Services\FileUpload\FileUpload;
use Illuminate\Http\Request;
use Modules\Company\Http\Requests\CreateCompanyRequest;
use Modules\Company\Models\Company;
use Modules\Job\Models\Job;
use Modules\Location\Models\Area;
use Modules\Location\Models\District;
use Modules\Location\Models\Division;
use Modules\Settings\Models\Setting;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Setting::isGroupCompany() && !Setting::isBranchEnabled()) {
            return $this->show(Company::find(1));
        }
        return view('mock.company.index', [
            'companies' => Company::with('parent')->paginate(10),
            'types' => Company::TYPES,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $level = intval($request->level);
        if ($level <= 0 || !array_key_exists($level, Company::TYPES))
            abort(404, 'Invalid company type');

        if (!Setting::isGroupCompany() && $level == Company::LEVEL_WING)
            abort(500, 'Group company is not enabled');

        if (!Setting::isBranchEnabled() && $level == Company::LEVEL_BRANCH)
            abort(500, 'Branch company is not enabled');

        return view('mock.company.form', [
            'company' => null,
            'weekdays' => json_encode(config('dataset.weekdays')),
            'parents' => $level > Company::LEVEL_COMPANY ? Company::select('id', 'name')->where('level', ($level - 1))->get()->pluck('name', 'id') : null,
            'divisions' => Division::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id'),
            'districts' => [],
            'areas' => [],
            'timezones' => config('dataset.timezones.all'),
            'level' => $level,
            'type' => Company::TYPES[$level]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCompanyRequest $request)
    {
        if (!Setting::isGroupCompany() && $request->level == Company::LEVEL_WING)
            return redirect()->back()->with('error', 'Group company is not enabled');

        if (!Setting::isBranchEnabled() && $request->level == Company::LEVEL_BRANCH)
            return redirect()->back()->with('error', 'Branch company is not enabled');

        $company = Company::create($request->only(
            'name', 'parent_id', 'code', 'email', 'address', 'city', 'district_id',
            'area_id', 'zipcode', 'phone', 'from_name', 'reg_number', 'tax_type', 'tax_number',
            'timezone', 'website', 'office_start_time', 'office_end_time', 'weekends', 'level'
        ));

        if ($company) {
            $company->logo = $request->hasFile('logo') ? uploadFile($request->logo, 'logo') : null;
            $company->save();

            return redirect()->route('mock.company.index')->with('success', 'Company created successfully');
        }

        return redirect()->back()->with('error', 'Company creation failed');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return $this->edit(company: $company);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        $parents = $company->level > Company::LEVEL_COMPANY ? Company::select('id', 'name')->where('level', ($company->level - 1))->get()->pluck('name', 'id') : null;
        $districts = District::select('id', 'name')->where('division_id', $company->district?->division_id)->orderBy('name')->get()->pluck('name', 'id');

        return view('mock.company.form', [
            'company' => $company,
            'weekdays' => json_encode(config('dataset.weekdays')),
            'parents' => $parents,
            'divisions' => Division::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id'),
            'districts' => $districts,
            'areas' => Area::where('district_id', $company->district_id)->orderBy('name')->get()->pluck('name', 'id'),
            'timezones' => config('dataset.timezones.all'),
            'level' => $company->level,
            'type' => Company::TYPES[$company->level]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $company->update($request->only(
            'name', 'parent_id', 'code', 'email', 'address', 'city', 'district_id',
            'area_id', 'zipcode', 'phone', 'from_name', 'reg_number', 'tax_type', 'tax_number',
            'timezone', 'website', 'office_start_time', 'office_end_time', 'weekends'
        ));

        if ($request->hasFile('logo')) {
            $company->logo && FileUpload::remove($company->logo);
            $company->logo = uploadFile($request->logo, 'logo');
            $company->save();
        }

        return redirect()->back()->with('success', Company::TYPES[$company->level] . ' updated successfully');
    }

    /**
     * Remove company wings & branches
     */
    public function destroy(Company $company)
    {
        if ($company->level == Company::LEVEL_COMPANY || $company->id == 1) {
            return redirect()->back()->with('error', 'Main company can not be deleted');
        } elseif (Job::where('wing_id', $company->id)->orWhere('branch_id', $company->id)->exists()) {
            return redirect()->back()->with('error', 'Company can not be deleted because it has jobs');
        }

        $company->delete();

        return redirect()->route('mock.company.index')->with('success', 'Company deleted successfully');
    }
}