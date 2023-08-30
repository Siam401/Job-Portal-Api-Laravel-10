<?php

namespace Modules\Applicant\Http\Controllers\Applicant;

use App\Services\FileUpload\FileUpload;
use App\Traits\ResponseJSON;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Applicant\Http\Resources\ResumeProfileResource;
use Modules\Applicant\Services\ApplicantService;
use Modules\User\Models\Applicant;
use Illuminate\Http\JsonResponse;

class ResumeController extends Controller
{
    use ResponseJSON;

    public function index()
    {
        $applicant = auth('sanctum')->user()->applicant;

        $data = Applicant::where('user_id', auth('sanctum')->user()->id)->with('personalInformation', 'experiences', 'educations', 'certifications', 'languages', 'metas', 'trainings', 'references')->first();

        if (!$applicant) {
            return $this->message('Applicant not found')->error();
        }

        return $this->success()->message('Applicant resume information is fetched successfully')->response(
            new ResumeProfileResource($data)
        );
    }

    /**
     * Get applicant resume file information
     *
     * @return JsonResponse
     */
    public function getFile()
    {
        $applicant = auth('sanctum')->user()->applicant;

        $resumeUploadTime = $applicant->resume ? FileUpload::getUploadTime($applicant->resume) : null;

        return $this->success()->message('Data fetched successfully')->response([
            'resume' => $applicant->resume ? FileUpload::getUrl($applicant->resume) : null,
            'upload_time' => $resumeUploadTime ? $resumeUploadTime->toIso8601String() : null,
        ]);
    }


    /**
     * Update applicant resume file
     *
     * @return JsonResponse
     */
    public function saveFile(Request $request)
    {

        $validator = validator($request->all(), [
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->message($validator->errors()->first())->error();
        }

        $applicant = Applicant::where('user_id', auth('sanctum')->user()->id)->first();

        if (!$applicant) {
            return $this->message('Applicant not found')->error(404);
        }

        (new ApplicantService($applicant))->updateResume($request->file('resume'));

        return $this->getFile();
    }

    /**
     * Remove applicant resume file
     *
     * @return JsonResponse
     */
    public function removeFile()
    {
        $applicant = auth('sanctum')->user()->applicant;

        if (!$applicant) {
            return $this->message('Applicant not found')->error(404);
        }

        if ($applicant->resume && FileUpload::remove($applicant->resume)) {
            $applicant->resume = null;
            $applicant->save();
        }

        return $this->success()->message('Applicant resume is removed successfully')->response();
    }

    /**
     * Get applicant resume completion status in percentage
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getResumeStatus(Request $request)
    {
        $applicant = $request->user()->applicant;

        return $this->success()->message('Applicant resume completion status retrieved')->response([
            'resume_completion_percentage' => (new ApplicantService($applicant))->resumeCompletionPercentage()
        ]);
    }

}