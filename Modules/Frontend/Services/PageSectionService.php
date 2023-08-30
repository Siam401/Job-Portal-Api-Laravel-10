<?php

namespace Modules\Frontend\Services;

use App\Services\FileUpload\FileUpload;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Frontend\Models\Section;
use Modules\Frontend\Models\SectionItem;
use Modules\Job\Models\JobFunction;

class PageSectionService
{

    public function __construct(protected Section $section, public string|null $error = null)
    {
        // auth('sanctum')->user()?->applicant;
    }

    /**
     * Update frontend section information
     *
     * @param array $data
     * @return boolean
     */
    public function updateSection(array $data): bool
    {
        DB::beginTransaction();

        try {

            $this->section->title = $data['title'];
            $this->section->subtitle = $data['subtitle'];
            $this->section->description = $data['description'] ?? null;
            if (isset($data['image']) && !empty($data['image'])) {
                $this->section->image && FileUpload::remove($this->section->image);
                $this->section->image = uploadFile($data['image']);
            }
            $this->section->is_active = $data['is_active'] ?? false;
            $this->section->save();

            if (isset($data['delete_items']) && !empty($data['delete_items'])) {
                SectionItem::where('section_id', $this->section->id)
                    ->whereIn('id', explode(',', $data['delete_items']))
                    ->delete();
            }

            switch ($this->section->slug) {
                case 'banner':
                    $this->saveBannerInformation($data);
                    break;
                case 'about-us':
                    $this->saveAboutInformation($data);
                    break;
                case 'facilities-benefits':
                    $this->saveBenefitsInformation($data);
                    break;
                case 'faq':
                    $this->saveFaqInformation($data);
                    break;
                case 'how-to-apply':
                    $this->saveHowToApplyInformation($data);
                    break;
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->error = $e->getMessage();
            return false;
        }

        return true;
    }

    /**
     * Save banner information
     *
     * @param array $data
     * @return void
     */
    protected function saveBannerInformation(array $data)
    {
        foreach ($data['section_items'] as $value) {
            $banner = new SectionItem();
            if (isset($value['id']) && !empty($value['id'])) {
                $banner = SectionItem::where('id', $value['id'])->where('section_id', $this->section->id)->first();
            } else {
                $banner->section_id = $this->section->id;
            }
            $banner->serial = $value['serial'] ?? 1;
            $banner->items = [
                'id' => intval($value['category']),
                'title' => JobFunction::find($value['category'])->name,
            ];
            $banner->save();
        }

    }

    /**
     * Save about us information
     *
     * @param array $data
     * @return void
     */
    public function saveAboutInformation(array $data)
    {
        foreach ($data['section_items'] as $value) {
            $about = new SectionItem();
            if (isset($value['id']) && !empty($value['id'])) {
                $about = SectionItem::where('id', $value['id'])->where('section_id', $this->section->id)->first();
            } else {
                $about->section_id = $this->section->id;
            }
            $about->serial = $value['serial'] ?? 1;
            $about->items = [
                'title' => $value['title'],
            ];
            $about->save();
        }
    }

    /**
     * Save facilities & benefits information
     *
     * @param array $data
     * @return void
     */
    public function saveBenefitsInformation(array $data)
    {

        foreach ($data['section_items'] as $value) {
            $sectionItem = new SectionItem();
            if (isset($value['id']) && !empty($value['id'])) {
                $sectionItem = SectionItem::where('id', $value['id'])->where('section_id', $this->section->id)->first();

                isset($value['image']) && isset($sectionItem->items['image_url']) && FileUpload::remove($sectionItem->items['image_url']);

            } else {
                $sectionItem->section_id = $this->section->id;
            }

            $sectionItem->serial = $value['serial'] ?? 1;
            $sectionItem->items = [
                'title' => $value['title'],
                'sub_title' => $value['sub_title'],
                'image_url' => isset($value['image']) && $value['image'] ? uploadFile($value['image']): $sectionItem->items['image_url'],
            ];
            $sectionItem->save();
        }

    }

    /**
     * Save faq information
     *
     * @param array $data
     * @return void
     */
    public function saveFaqInformation(array $data)
    {
        foreach ($data['section_items'] as $value) {
            $sectionItem = new SectionItem();
            if (isset($value['id']) && !empty($value['id'])) {
                $sectionItem = SectionItem::where('id', $value['id'])->where('section_id', $this->section->id)->first();
            } else {
                $sectionItem->section_id = $this->section->id;
            }
            $sectionItem->serial = $value['serial'] ?? 1;

            $sectionItem->items = [
                'title' => $value['title'],
                'sub_title' => $value['sub_title'],
            ];
            $sectionItem->save();
        }

    }

    /**
     * Save how to apply information
     *
     * @param array $data
     * @return void
     */
    public function saveHowToApplyInformation(array $data)
    {
        foreach ($data['section_items'] as $value) {
            $sectionItem = new SectionItem();
            if (isset($value['id']) && !empty($value['id'])) {
                $sectionItem = SectionItem::where('id', $value['id'])->where('section_id', $this->section->id)->first();
            } else {
                $sectionItem->section_id = $this->section->id;
            }
            $sectionItem->serial = $value['serial'] ?? 1;
            $sectionItem->items = [
                'title' => $value['title'],
                'sub_title' => $value['sub_title'],
            ];
            $sectionItem->save();
        }
    }

}