<?php

namespace App\Http\Controllers\Mock;

use App\Http\Controllers\Controller;
use App\Services\FileUpload\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Job\Models\Job;
use Modules\Job\Services\FrontendService;
use Modules\JobApplication\Models\JobApplication;
use Modules\User\Models\Applicant;

class IndexController extends Controller
{
    public function __invoke(Request $request)
    {

        // $url = "https://lh3.googleusercontent.com/a/AAcHTtcwSTAwQDXyqnoKK9MBEDVXavXdFMlhkaNUbAabrCA-=s96-c";

        // $contents = file_get_contents($url);
        // $name = 'download' . rand(100, 999) . '.png';

        // if (Storage::put($name, $contents)) {
        //     $request->files->set('photo', new UploadedFile(Storage::path($name), $name));
        //     $upload = uploadFile($request->file('photo'), 'photo');

        //     if ($upload) {
        //         unlink(Storage::path($name));
        //     }
        //     dd(FileUpload::getUrl($upload));
        // }

        // dd((new FrontendService)->jobCountByCities(), (new FrontendService)->jobCountByWings());
        //https://upload.wikimedia.org/wikipedia/commons/6/69/Airbnb_Logo_B%C3%A9lo.svg

        return view('mock.index', [
            'jobs' => Job::active()->count(),
            'applicants' => Applicant::count(),
            'applications' => JobApplication::whereNotIn('stage', [
                JobApplication::STAGE_REJECTED,
                JobApplication::STAGE_HIRED,
            ])->count(),
        ]);
    }
}