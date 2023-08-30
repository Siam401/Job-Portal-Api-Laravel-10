<?php

namespace App\Http\Controllers\Mock;

use App\Http\Controllers\Controller;
use App\Services\FileUpload\FileUpload;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Modules\Frontend\Models\SocialLink;
use Modules\Settings\Models\Setting;
use Modules\Settings\Services\SystemService;
use Throwable;

class SettingsController extends Controller
{
    /**
     * System configuration page
     */
    public function index()
    {
        return view('mock.settings.index', [
            'name' => 'system',
            'configs' => Setting::whereIn('name', [
                'is_group_company',
                'is_ngo',
                'has_branch',
                'maintenance_mode',
                'maintenance_description'
            ])->orderBy('id')->get(),
            'theme' => Setting::whereIn('name', [
                'site_title',
                'site_description',
                'site_logo',
                'site_favicon',
                'footer_text',
                'footer_logo',
            ])->orderBy('id')->get(),
            'seo' => Setting::whereIn('name', [
                'seo_title',
                'seo_description',
                'seo_keywords',
                'seo_image',
            ])->orderBy('id')->get(),
            'other' => Setting::whereIn('name', [
                'support_email',
                'support_phone',
                'support_hotline',
            ])->get(),
        ]);
    }

    /**
     * Update settings values
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request)
    {
        /**
         *  Update Config Settings
         */
        if ($request->has('is_group_company')) {
            Setting::saveOption('is_group_company', $request->is_group_company, 'boolean');
        }
        if ($request->has('is_ngo')) {
            Setting::saveOption('is_ngo', $request->is_ngo, 'boolean');
        }
        if ($request->has('has_branch')) {
            Setting::saveOption('has_branch', $request->has_branch, 'boolean');
        }
        if ($request->has('maintenance_mode')) {
            Setting::saveOption('maintenance_mode', $request->maintenance_mode, 'boolean');
        }
        if ($request->has('maintenance_description')) {
            Setting::saveOption('maintenance_description', $request->maintenance_description, 'text');
        }

        /**
         * Update Theme Settings
         */
        if ($request->has('site_title')) {
            Setting::saveOption('site_title', $request->site_title, 'string');
        }
        if ($request->has('site_description')) {
            Setting::saveOption('site_description', $request->site_description, 'text');
        }
        if ($request->hasFile('site_logo')) {
            $logo = uploadFile($request->site_logo);
            if ($logo) {
                FileUpload::remove(Setting::getOption('site_logo'));
                Setting::saveOption('site_logo', $logo, 'image');
            }
        }
        if ($request->hasFile('site_favicon')) {
            $logo = uploadFile($request->site_favicon);
            if ($logo) {
                FileUpload::remove(Setting::getOption('site_favicon'));
                Setting::saveOption('site_favicon', $logo, 'image');
            }
        }
        if ($request->has('footer_text')) {
            Setting::saveOption('footer_text', $request->footer_text, 'text');
        }
        if ($request->hasFile('footer_logo')) {
            $logo = uploadFile($request->footer_logo);
            if ($logo) {
                FileUpload::remove(Setting::getOption('footer_logo'));
                Setting::saveOption('footer_logo', $logo, 'image');
            }
        }

        /**
         * Update SEO Settings
         */
        if ($request->has('seo_title')) {
            Setting::saveOption('seo_title', $request->seo_title, 'string');
        }
        if ($request->has('seo_description')) {
            Setting::saveOption('seo_description', $request->seo_description, 'text');
        }
        if ($request->has('seo_keywords')) {
            Setting::saveOption('seo_keywords', $request->seo_keywords, 'string');
        }
        if ($request->hasFile('seo_image')) {
            $logo = uploadFile($request->seo_image);
            if ($logo) {
                FileUpload::remove(Setting::getOption('seo_image'));
                Setting::saveOption('seo_image', $logo, 'image');
            }
        }

        /**
         * Update Other Settings
         */
        if ($request->has('support_email')) {
            Setting::saveOption('support_email', $request->support_email, 'string');
        }
        if ($request->has('support_phone')) {
            Setting::saveOption('support_phone', $request->support_phone, 'string');
        }
        if ($request->has('support_hotline')) {
            Setting::saveOption('support_hotline', $request->support_hotline, 'string');
        }

        Cache::flush();

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Get / Save Social Links
     *
     * @param Request $request
     * @return Responsable|RedirectResponse|Renderable
     */
    public function socialLinks(Request $request)
    {
        if ($request->social_links) {
            try {
                foreach ($request->social_links as $key => $value) {
                    //code...
                    SocialLink::updateOrCreate([
                        'title' => $key,
                    ], [
                        'url' => $value['url'],
                        'icon_image' => $value['icon_image'],
                        'serial' => $value['serial'] ?? 1,
                        'is_active' => $value['is_active'] ?? 0,
                    ]);
                }

                session()->flash('success', 'Social links updated successfully.');
            } catch (\Throwable $th) {
                return back()->with('error', $th->getMessage());
            }

            Cache::forget('social_links');
        }

        return view('mock.settings.social-links', [
            'socialLinks' => SocialLink::orderBy('serial')->get()
        ]);
    }

    /**
     * Get / Save Social Authentication Keys
     *
     * @param Request $request
     * @return Responsable|RedirectResponse|Renderable
     */
    public function socialAuths(Request $request)
    {
        if ($request->method('post')) {
            try {
                Setting::updateArray($request->except('_token'));

                session()->flash('success', 'Social-Auth params updated successfully.');
            } catch (\Throwable $th) {
                return back()->with('error', $th->getMessage());
            }
        }

        return view('mock.settings.social-auths');
    }

    public function getEnvironment(Request $request)
    {
        return view('mock.settings.environment', [
            'configs' => [
                'app' => Setting::whereIn('name', [
                    'frontend_url', 'demo_test', 'time_otp_resend', 'time_otp_lifetime', 'time_zone', 'app_debug'
                ])->get(),
                'mail' => Setting::where('name', 'like', 'mail_%')->get(),
                'sms' => Setting::where('name', 'like', 'sms_%')->get(),
            ],
            'active' => $request->active ?? 'app'
        ]);
    }

    public function saveEnvironment(Request $request)
    {

        try {

            if ($request->env) {
                $settings = [];
                foreach ($request->env as $key => $value) {
                    $settings[] = [
                        'name' => $key,
                        'value' => dataConvert($value, $request->data_type[$key]),
                        'data_type' => $request->data_type[$key]
                    ];
                }

                SystemService::setEnvironmentValue(collect($settings)->pluck('value', 'name')->toArray());

                Setting::updateArray($settings);

                Artisan::call('optimize:clear');

                return redirect()->route('mock.settings.environment.index', ['active' => $request->active])
                    ->with('success', 'Environment settings updated successfully.');
            } else {
                throw new Exception('Environment settings data not found.');
            }

        } catch (Exception $e) {
            report($e);
            return redirect()->back()
                ->with('error', $e->getMessage());
        }

    }
}