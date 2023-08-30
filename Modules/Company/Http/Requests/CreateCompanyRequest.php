<?php

namespace Modules\Company\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCompanyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'level' => 'required|integer',
            'parent' => 'nullable|integer',
            'code' => 'sometimes|unique:companies,code',
            'email' => 'required|email',
            'address' => 'required|string',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'city' => 'required|string',
            'district_id' => 'required|exists:districts,id',
            'area_id' => 'nullable|integer',
            'zipcode' => 'nullable|string',
            'phone' => 'required|string|min:10|max:15',
            'from_name' => 'required|string|min:4',
            'reg_number' => 'nullable|string',
            'tax_type' => 'nullable|string',
            'tax_number' => 'nullable|string',
            'timezone' => 'nullable|string',
            'weekends' => 'nullable|array',
            'website' => 'nullable|url',
            'start_time' => 'nullable|date_format:H:i a',
            'end_time' => 'nullable|date_format:H:i a',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'code' => strtoupper(slug($this->name)),
        ]);
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