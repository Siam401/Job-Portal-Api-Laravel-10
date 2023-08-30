<?php

namespace Modules\Frontend\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Frontend\Utils\PageSectionValidation;

class SaveFrontendSectionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [
            'name' => 'required|string',
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:500',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'sometimes|boolean',
            'delete_items' => 'nullable|string',
        ];

        $rules = array_merge(
            $rules,
            PageSectionValidation::getValidationRules(request()->get('name') ?? 'banner')
        );

        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
