<?php

namespace Modules\Applicant\Http\Controllers\Applicant;

use App\Traits\ResponseJSON;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Applicant\Http\Requests\ApplicantInformationRequest;
use Modules\Applicant\Models\ApplicantCertification;
use Modules\Applicant\Models\ApplicantEducation;
use Modules\Applicant\Models\ApplicantExperience;
use Modules\Applicant\Models\ApplicantLanguage;
use Modules\Applicant\Models\ApplicantReference;
use Modules\Applicant\Models\ApplicantTraining;
use Modules\Applicant\Services\ApplicantInformationService;

class InformationController extends Controller
{
    use ResponseJSON;

    /**
     * Get Applicant Resume Profile Information
     *
     * @return JsonResponse
     */
    public function index(Request $request, string $category = null)
    {
        $category = $category ?? $request->category ?? 'personal';

        $applicant = auth('sanctum')->user()->applicant;

        if (!$applicant) {
            return $this->message('Applicant not found')->error(404);
        }

        $applicantInformationService = new ApplicantInformationService($applicant);

        $data = match ($category) {
            'education' => $applicantInformationService->getAcademicInformation(),
            'training' => $applicantInformationService->getTrainingInformation(),
            'certification' => $applicantInformationService->getCertificationInformation(),
            'experience' => $applicantInformationService->getEmploymentHistory(),
            'specialization' => $applicantInformationService->getSpecializationInformation(),
            'language' => $applicantInformationService->getLanguageInformation(),
            'reference' => $applicantInformationService->getReferenceInformation(),
            default => $applicantInformationService->getPersonalInformation(),
        };

        if (empty($data)) {
            return $this->success()->message('Applicant ' . $category . ' information is unavailable')->response();
        }

        return $this->success()->message('Applicant ' . $category . ' information is fetched successfully')->response($data);
    }

    /**
     * Save Applicant Resume Profile Information
     *
     * @param ApplicantInformationRequest $request
     * @return JsonResponse
     */
    public function saveInformation(ApplicantInformationRequest $request)
    {

        $applicant = auth('sanctum')->user()->applicant;

        if (!$applicant) {
            return $this->message('Applicant not found')->error(404);
        }

        $applicantService = new ApplicantInformationService($applicant);

        $result = match ($request->category) {
            'personal' => $applicantService->savePersonalInformation($request->validated()),
            'education' => $applicantService->saveAcademicInformation($request->validated()),
            'training' => $applicantService->saveTrainingInformation($request->validated()),
            'certification' => $applicantService->saveCertificationInformation($request->validated()),
            'experience' => $applicantService->saveEmploymentHistory($request->validated()),
            'specialization' => $applicantService->saveSpecializationInformation($request->validated()),
            'language' => $applicantService->saveLanguageInformation($request->validated()),
            'reference' => $applicantService->saveReferenceInformation($request->validated()),
            default => false,
        };

        if (is_array($result) && !empty($result)) {
            return $this->success()->message('Applicant ' . $request->category . ' information saved successfully')->response($result);
        }

        return $this->message(
            $applicantService->error ?? 'Failure: Applicant ' . $request->category . ' information not saved'
        )->error();
    }

    /**
     * Remove Applicant Resume Profile Information
     *
     * @param string $category
     * @param integer $id
     * @return JsonResponse
     */
    public function remove(string $category, int $id)
    {
        $applicant = auth('sanctum')->user()->applicant;

        $model = match ($category) {
            'education' => ApplicantEducation::query(),
            'training' => ApplicantTraining::query(),
            'certification' => ApplicantCertification::query(),
            'experience' => ApplicantExperience::query(),
            'language' => ApplicantLanguage::query(),
            'reference' => ApplicantReference::query(),
            default => throw new HttpResponseException(
                $this->message('Invalid category')->error()
            ),
        };

        DB::beginTransaction();
        try {
            $result = $model->where('id', $id)->where('applicant_id', $applicant->id)->delete();

            $applicant->degradeProfileResumeStatus(
                category: $category,
                count: $model->where('applicant_id', $applicant->id)->count()
            );
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return $this->message($e->getMessage())->error(500);
        }

        if (!$result || $result === 0) {
            return $this->message('Applicant ' . $category . ' information not found')->error(404);
        }

        return $this->success()->message('Applicant ' . $category . ' information has been deleted')->response();
    }
}