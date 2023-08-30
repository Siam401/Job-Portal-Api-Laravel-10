<?php

namespace App\Http\Controllers\Mock;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Company\Models\Company;
use Modules\Job\Models\Job;
use Modules\Job\Models\JobDetail;
use Modules\Job\Models\JobFunction;
use Modules\Job\Models\JobQuestion;
use Modules\Settings\Models\FormOption;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('mock.job.index', [
            'jobs' => Job::with('jobFunction')->latest('id')->paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ages = [];
        for ($i = 18; $i <= 45; $i++) {
            $ages[$i] = $i;
        }

        $exps = range(0, 16);

        return view('mock.job.create', [
            'wings' => Company::select('id', 'name')->wings()->get()->pluck('name', 'id'),
            'branches' => Company::select('id', 'name')->branch()->get()->pluck('name', 'id'),
            'categories' => JobFunction::active()->get()->pluck('name', 'id'),
            'questions' => JobQuestion::get(),
            'workplaces' => FormOption::select('option_title', 'option_value')->where('name', 'workplace')->get()->pluck('option_title', 'option_value'),
            'jobTypes' => FormOption::select('option_title', 'option_value')->where('name', 'job_type')->get()->pluck('option_title', 'option_value'),
            'statusList' => FormOption::select('option_title', 'option_value')->where('name', 'job_status')->get()->pluck('option_title', 'option_value'),
            'genderList' => FormOption::select('option_title', 'option_value')->where('name', 'gender')->get()->pluck('option_title', 'option_value'),
            'ages' => $ages,
            'exps' => $exps
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $job = Job::create([
                'category' => 'default',
                'title' => $request->title,
                'wing_id' => $request->wing_id,
                'branch_id' => $request->branch_id,
                'job_function_id' => $request->job_function_id,
                'vacancy' => $request->vacancy_na ? null : $request->vacancy,
                'code' => date('Ymd') . rand(1000, 9999),
                'status' => intval($request->status),
                'start_date' => $request->start_date ? Carbon::parse($request->start_date)->toDateString() : Carbon::now()->today()->toDateString(),
                'end_date' => $request->end_date ? Carbon::parse($request->end_date)->toDateString() : Carbon::now()->addDays(10)->toDateString()

            ]);

            JobDetail::create([
                'job_id' => $job->id,
                'job_type' => $request->job_type,
                'workplace' => $request->workplace,
                'salary' => $request->salary,
                'gender' => $request->gender,
                'form_visibility' => implode(',', $request->form_visibility ?? []),
                'questions' => implode(',', $request->questions ?? []),
                'skills' => $request->skills,
                'age_min' => $request->age_min,
                'age_max' => $request->age_max,
                'description' => $request->description,
                'responsibility' => $request->responsibility,
                'education' => $request->education,
                'benefit' => $request->benefit,
                'additional' => $request->additional,
                'experience' => $request->experience,
                'is_exp_required' => $request->is_exp_required ? 1 : 0,
                'min_exp' => $request->min_exp,
                'max_exp' => $request->max_exp,
            ]);

            DB::commit(); // finally commit to database
        } catch (\Exception $e) {
            DB::rollback(); // roll back if any error occurs

            return redirect()->route('mock.job.index')->with('error', $e->getMessage());
        }

        return redirect()->route('mock.job.index')->with('success', 'Job created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Job $job)
    {
        return $this->edit($job);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Job $job)
    {
        $ages = [];
        for ($i = 18; $i <= 45; $i++) {
            $ages[$i] = $i;
        }

        $exps = range(0, 16);

        return view('mock.job.edit', [
            'wings' => Company::select('id', 'name')->wings()->get()->pluck('name', 'id'),
            'branches' => Company::select('id', 'name')->branch()->get()->pluck('name', 'id'),
            'categories' => JobFunction::active()->get()->pluck('name', 'id'),
            'questions' => JobQuestion::get(),
            'workplaces' => FormOption::select('option_title', 'option_value')->where('name', 'workplace')->get()->pluck('option_title', 'option_value'),
            'jobTypes' => FormOption::select('option_title', 'option_value')->where('name', 'job_type')->get()->pluck('option_title', 'option_value'),
            'statusList' => FormOption::select('option_title', 'option_value')->where('name', 'job_status')->get()->pluck('option_title', 'option_value'),
            'genderList' => FormOption::select('option_title', 'option_value')->where('name', 'gender')->get()->pluck('option_title', 'option_value'),
            'ages' => $ages,
            'exps' => $exps,
            'job' => $job,
            'jobDetail' => $job->detail ?? null
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Job $job)
    {
        DB::beginTransaction();

        try {

            $job->update([
                'title' => $request->title,
                'wing_id' => $request->wing_id,
                'branch_id' => $request->branch_id,
                'job_function_id' => $request->job_function_id,
                'vacancy' => $request->vacancy_na ? null : $request->vacancy,
                'status' => intval($request->status),
                'start_date' => $request->start_date ? Carbon::parse($request->start_date)->toDateString() : Carbon::now()->today()->toDateString(),
                'end_date' => $request->end_date ? Carbon::parse($request->end_date)->toDateString() : Carbon::now()->addDays(10)->toDateString()

            ]);

            $job->detail()->update([
                'job_type' => $request->job_type,
                'workplace' => $request->workplace,
                'salary' => $request->salary,
                'gender' => $request->gender,
                'form_visibility' => implode(',', $request->form_visibility ?? []),
                'questions' => implode(',', $request->questions ?? []),
                'skills' => $request->skills,
                'age_min' => $request->age_min,
                'age_max' => $request->age_max,
                'description' => $request->description,
                'responsibility' => $request->responsibility,
                'education' => $request->education,
                'benefit' => $request->benefit,
                'additional' => $request->additional,
                'is_exp_required' => $request->is_exp_required ? 1 : 0,
                'experience' => $request->is_exp_required ? $request->experience : null,
                'min_exp' => $request->min_exp,
                'max_exp' => $request->max_exp,
            ]);

            DB::commit(); // roll back if any error occurs
        } catch (\Exception $e) {
            DB::rollback(); // roll back if any error occurs

            return redirect()->route('mock.job.index')->with('error', $e->getMessage());
        }

        return back()->with('success', 'Job updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job)
    {
        if ($job->applications()->exists()) {
            return redirect()->route('mock.job.index')->with('error', 'Error! Job has applications');
        }

        if ($job->delete()) {
            return redirect()->route('mock.job.index')->with('success', 'Success! Job deleted successfully');
        }

        return redirect()->route('mock.job.index')->with('error', 'Error! Job could not be deleted');
    }
}