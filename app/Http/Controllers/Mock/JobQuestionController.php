<?php

namespace App\Http\Controllers\Mock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Job\Models\JobQuestion;

class JobQuestionController extends Controller
{
    public function index()
    {

        $jobQuestions = JobQuestion::all();

        return view('mock.job-question.index', compact('jobQuestions'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*.question' => 'required|string',
            'questions.*.is_required' => 'sometimes|boolean',
            'questions.*.is_active' => 'sometimes|boolean',
        ]);

        try {

            if ($request->delete_items) {
                JobQuestion::whereIn('id', explode(',', $request->delete_items))->delete();
            }

            foreach ($request->questions as $value) {
                $question = new JobQuestion();
                if (isset($value['id']) && !empty($value['id'])) {
                    $question = JobQuestion::where('id', $value['id'])->first();
                }
                $question->question = $value['question'];
                $question->is_required = $value['is_required'] ?? 0;
                $question->is_active = $value['is_active'] ?? 0;

                $question->save();
            }
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }

        return back()->with('success', 'Job questions saved successfully');
    }

}