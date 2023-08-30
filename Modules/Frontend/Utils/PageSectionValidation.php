<?php

namespace Modules\Frontend\Utils;


class PageSectionValidation
{
    public static function getValidationRules(string $sectionName): array
    {
        $validationRules = self::rules();

        return $validationRules[$sectionName] ?? [];
    }

    /**
     * Get the validation rules that apply to the Application information update request.
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'banner' => [
                'section_items' => 'required|array|min:1',
                'section_items.*.id' => 'nullable|integer',
                'section_items.*.serial' => 'nullable|integer',
                'section_items.*.category' => 'required|integer',
            ],

            'about-us' => [
                'description' => 'required|string',
                'section_items' => 'required|array|min:1',
                'section_items.*.id' => 'nullable|integer',
                'section_items.*.extra_text' => 'nullable|string',
                'section_items.*.title' => 'required|string',
                'section_items.*.serial' => 'nullable|integer',
            ],

            'facilities-benefits' => [
                'description' => 'required|string',
                'section_items' => 'required|array|min:1',
                'section_items.*.id' => 'nullable|integer',
                'section_items.*.title' => 'required|string',
                'section_items.*.sub_title' => 'required|string',
                'section_items.*.image' => 'nullable|file|max:2048|mimes:jpeg,jpg,png,gif',
                'section_items.*.serial' => 'nullable|integer',
            ],
            'faq' => [
                'description' => 'required|string',
                'section_items' => 'required|array|min:1',
                'section_items.*.id' => 'nullable|integer',
                'section_items.*.title' => 'required|string',
                'section_items.*.sub_title' => 'required|string',
                'section_items.*.serial' => 'nullable|integer',
            ],

            'how-to-apply' => [
                'section_items' => 'required|array|min:1',
                'section_items.*.id' => 'nullable|integer',
                'section_items.*.title' => 'required|string',
                'section_items.*.sub_title' => 'required|string',
                'section_items.*.serial' => 'nullable|integer',

            ],
            'job-wings' => [],
            'job-cities' => [
                'description' => 'nullable|string',
            ],

        ];
    }
}