<?php

namespace Modules\JobApplication\Services;

use App\Services\FileUpload\FileUpload;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Applicant\Services\ApplicantService;
use Modules\Job\Models\Job;
use Modules\JobApplication\Models\JobApplication;
use Modules\User\Models\Applicant;

class JobApplyService
{
    /**
     * Request
     *
     * @var [Illuminate\Http\Request]
     */
    public $request;

    /**
     * Job model
     *
     * @var [Modules\Job\Models\Job]
     */
    public $job;

    /**
     * Applicant model
     *
     * @var [Modules\User\Models\Applicant]
     */
    public $applicant;

    /**
     * Job Application model
     *
     * @var [false|string]
     */
    public $jobApplication;

    /**
     * Error message
     *
     * @var [false|string]
     */
    public $error;

    public function __construct(Request $request, Job $job)
    {
        $this->request = $request;
        $this->job = $job;
        $this->applicant = new Applicant();
        $this->error = false;
        $this->jobApplication = new JobApplication();
    }

    public function handle()
    {
        DB::beginTransaction();

        try {

            if (isApplicantUser()) {
                $this->applicant = Applicant::find(auth('sanctum')->user()->id);
                if(empty($this->applicant)) {
                    throw new Exception('Applicant not found');
                } elseif(empty($this->applicant->resume) && $this->request->hasFile('resume')) {
                    $this->applicant->resume = $this->handleApplicantResume();
                    $this->applicant->save();
                }
            } else {
                $this->findOrCreateApplicant();
            }

            if (JobApplication::where('job_id', $this->job->id)->where('applicant_id', $this->applicant->id)->exists()) {
                throw new Exception('You already applied for this job');
            }

            $this->submitApplication();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            throw new Exception($e->getMessage());

        }

        // Parse applicant resume and save to resume profile tables if possible
        (new ApplicantService($this->applicant))->parseResumeIfNeeded();

        return $this;
    }

    protected function findOrCreateApplicant()
    {
        $this->applicant = Applicant::where('email', $this->request->email)->orWhere('mobile')->first();

        if (empty($applicant)) {
            $this->applicant = Applicant::create([
                'first_name' => trim($this->request->first_name),
                'last_name' => $this->request->last_name,
                'email' => $this->request->email,
                'country_code' => $this->request->country_code ?? '880',
                'mobile' => $this->request->mobile,
                'gender' => $this->request->gender ?? 'male',
                'resume' => $this->request->hasFile('resume') ? $this->handleApplicantResume() : null,
                'photo' => $this->request->hasFile('photo') ? uploadFile($this->request->file('photo'), 'photo', true) : null,

            ]);
        }

        if (empty($this->applicant)) {
            throw new Exception('Failed to create applicant');
        }
    }

    protected function submitApplication()
    {

        $this->jobApplication = $this->jobApplication->create([
            'job_id' => $this->job->id,
            'applicant_id' => $this->applicant->id,
            'photo' => $this->applicant->photo,
            'cover_letter' => $this->request->cover_letter,
            'stage' => JobApplication::STAGE_APPLIED,
            'sequence' => 1,
            'is_seen' => false,
        ]);

        if ($this->request->has('questions') && !empty($this->request->questions)) {
            foreach ($this->request->questions as $val) {
                if (!isset($val['question']) || empty($val['question']))
                    continue;

                $this->jobApplication->questionAnswers()->create([
                    'question' => $val['question'],
                    'answer' => $val['answer'] ?? null,
                ]);
            }
        }

        return $this;
    }

    protected function handleApplicantResume()
    {
        if (!empty($this->applicant->resume)) {
            FileUpload::remove($this->applicant->resume);
        }

        return uploadFile($this->request->file('resume'), 'resume', true);
    }

}