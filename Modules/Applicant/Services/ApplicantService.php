<?php

namespace Modules\Applicant\Services;

use App\Services\FileUpload\FileUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use App\Services\CvParser\Handler as CvParserHandler;
use Modules\User\Models\Applicant;

class ApplicantService
{
    public function __construct(protected Applicant $applicant)
    {

    }

    public static function getApplicant()
    {
        return auth('sanctum')->user()?->applicant;
    }

    public function parseResumeIfNeeded()
    {
        if (isTrue($this->applicant->is_cv_parsed) || empty($this->applicant->resume) || !FileUpload::exists($this->applicant->resume)) {
            return false;
        }
        $parsedData = (new CvParserHandler)->process(FileUpload::storagePath($this->applicant->resume));

        return is_array($parsedData) && $parsedData ? (new ApplicantInformationService($this->applicant))->saveCvData($parsedData) : false;
    }

    public function updateResume(UploadedFile $resumeFile)
    {
        if (!empty($this->applicant->resume)) {
            FileUpload::remove($this->applicant->resume);
        }

        $this->applicant->update([
            'resume' => uploadFile($resumeFile, 'resume')
        ]);

        return $this->applicant->resume;
    }

    /**
     * Get applicant resume completion percentage out of 100
     *
     * @return bool
     */
    public function resumeCompletionPercentage(): int
    {
        return Cache::remember('resume_completion_status_' . $this->applicant->id, 60 * 60 * 24, function () {

            $metaData = $this->applicant->metas()->where('key_name', 'like', 'profile_status_%')->get();
            $result = 0;
            foreach ($metaData as $data) {
                $result += intval($data->key_value);
            }

            return $result <= 0 ? 0 : ($result > 100 ? 100 : $result);
        });
    }

}