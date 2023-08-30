<?php

namespace App\Http\Controllers\Mock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Frontend\Http\Requests\SaveFrontendSectionRequest;
use Modules\Frontend\Models\Section;
use Modules\Frontend\Services\PageSectionService;
use Modules\Job\Models\JobFunction;

class FrontendController extends Controller
{
    public function index(Request $request)
    {

        $models = Section::with('sectionItems')->get();
        $sections = [];
        foreach ($models as $section) {
            $sections[$section->slug] = $section;
        }

        return view('mock.frontend.index', [
            'sections' => $sections,
            'active' => $request->name && array_key_exists($request->name, $sections) ? $request->name : 'banner',
            'categories' => JobFunction::active()->get()->pluck('name', 'id'),
        ]);
    }

    public function update(SaveFrontendSectionRequest $request)
    {

        $section = Section::where('slug', $request->name)->first();
        if (!$section) {
            return redirect()->back()->with('error', 'Section not found');
        }

        $pageSectionService = new PageSectionService($section);
        $pageSectionService->updateSection($request->validated());

        if ($pageSectionService->error) {
            return redirect()->route('mock.frontend', ['name' => $request->name])->with(
                'error',
                $pageSectionService->error ?? 'Failed to save section information'
            );
        }

        return redirect()->route('mock.frontend', ['name' => $request->name])->with(
            'success',
            'Section information saved successfully'
        );

    }
}