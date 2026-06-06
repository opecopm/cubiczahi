<?php

namespace Modules\CMS\Livewire\WebSettings;

use Livewire\Component;
use Modules\CMS\Models\WebSetting;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public $locale = 'en';

    public $site_name = [];
    public $footer_about = [];
    public $footer_text = [];

    public $contact_email;
    public $contact_phone;
    public $contact_address;
    public $social_whatsapp;
    public $social_facebook;
    public $social_twitter;
    public $social_instagram;
    public $social_linkedin;
    
    public $map_iframe;

    public $header_logo = null;
    public $footer_logo = null;
    public $site_favicon = null;

    public $mediaComponentNames = ['header_logo', 'footer_logo', 'site_favicon'];

    public function mount()
    {
        $this->locale = app()->getLocale();
        $this->site_name = WebSetting::firstOrCreate(['key' => 'site_name'])->getTranslations('value');
        $this->footer_about = WebSetting::firstOrCreate(['key' => 'footer_about'])->getTranslations('value');
        $this->footer_text = WebSetting::firstOrCreate(['key' => 'footer_text'])->getTranslations('value');

        $this->contact_email = WebSetting::firstOrCreate(['key' => 'contact_email'])->value;
        $this->contact_phone = WebSetting::firstOrCreate(['key' => 'contact_phone'])->value;
        $this->contact_address = WebSetting::firstOrCreate(['key' => 'contact_address'])->value;
        $this->map_iframe = WebSetting::firstOrCreate(['key' => 'map_iframe'])->value;
        $this->header_logo = WebSetting::firstOrCreate(['key' => 'header_logo'])->value;
        $this->footer_logo = WebSetting::firstOrCreate(['key' => 'footer_logo'])->value;
        $this->site_favicon = WebSetting::firstOrCreate(['key' => 'site_favicon'])->value;
        $this->social_facebook = WebSetting::firstOrCreate(['key' => 'social_facebook'])->value;
        $this->social_twitter = WebSetting::firstOrCreate(['key' => 'social_twitter'])->value;
        $this->social_instagram = WebSetting::firstOrCreate(['key' => 'social_instagram'])->value;
        $this->social_linkedin = WebSetting::firstOrCreate(['key' => 'social_linkedin'])->value;
    }

    public function save()
    {
        $siteName = WebSetting::firstOrCreate(['key' => 'site_name']);
        $siteName->setTranslation('value', $this->locale, $this->site_name[$this->locale] ?? '');
        $siteName->save();

        $footerAbout = WebSetting::firstOrCreate(['key' => 'footer_about']);
        $footerAbout->setTranslation('value', $this->locale, $this->footer_about[$this->locale] ?? '');
        $footerAbout->save();

        $footerText = WebSetting::firstOrCreate(['key' => 'footer_text']);
        $footerText->setTranslation('value', $this->locale, $this->footer_text[$this->locale] ?? '');
        $footerText->save();

        WebSetting::updateOrCreate(['key' => 'contact_email'], ['value' => $this->contact_email]);
        WebSetting::updateOrCreate(['key' => 'contact_phone'], ['value' => $this->contact_phone]);
        WebSetting::updateOrCreate(['key' => 'contact_address'], ['value' => $this->contact_address]);
        WebSetting::updateOrCreate(['key' => 'social_whatsapp'], ['value' => $this->social_whatsapp]);
        WebSetting::updateOrCreate(['key' => 'social_facebook'], ['value' => $this->social_facebook]);
        WebSetting::updateOrCreate(['key' => 'social_twitter'], ['value' => $this->social_twitter]);
        WebSetting::updateOrCreate(['key' => 'social_instagram'], ['value' => $this->social_instagram]);
        WebSetting::updateOrCreate(['key' => 'social_linkedin'], ['value' => $this->social_linkedin]);
        WebSetting::updateOrCreate(['key' => 'map_iframe'], ['value' => $this->map_iframe]);

        // Save Header & Footer Logo
        if (@$this->header_logo) {
            $headerLogo = WebSetting::firstOrCreate(['key' => 'header_logo']);
            $headerLogo->clearMediaCollection('header_logo');
            $headerLogo->addMedia($this->header_logo->getRealPath())->toMediaCollection('header_logo');
        }

        if (@$this->footer_logo) {
            $footerLogo = WebSetting::firstOrCreate(['key' => 'footer_logo']);
            $footerLogo->clearMediaCollection('footer_logo');
            $footerLogo->addMedia($this->footer_logo->getRealPath())->toMediaCollection('footer_logo');
        }

        if (@$this->site_favicon) {
            $siteFavicon = WebSetting::firstOrCreate(['key' => 'site_favicon']);
            $siteFavicon->clearMediaCollection('site_favicon');
            $siteFavicon->addMedia($this->site_favicon->getRealPath())->toMediaCollection('site_favicon');
        }

        session()->flash('success', 'Website settings updated successfully!');
        return redirect()->route('admin.cms.web-settings.index');
    }

    public function deleteHeaderLogo()
    {
        $setting = WebSetting::where('key', 'header_logo')->first();
        if ($setting) {
            $setting->clearMediaCollection('header_logo');
        }
        $this->header_logo = null;
        session()->flash('success', 'Header logo removed.');
        // Refresh component
        return redirect()->route('admin.cms.web-settings.index');
    }

    public function deleteFooterLogo()
    {
        $setting = WebSetting::where('key', 'footer_logo')->first();
        if ($setting) {
            $setting->clearMediaCollection('footer_logo');
        }
        $this->footer_logo = null;
        session()->flash('success', 'Footer logo removed.');
        // Refresh component
        return redirect()->route('admin.cms.web-settings.index');
    }

    public function deleteSiteFavicon()
    {
        $setting = WebSetting::where('key', 'site_favicon')->first();
        if ($setting) {
            $setting->clearMediaCollection('site_favicon');
        }
        $this->site_favicon = null;
        session()->flash('success', 'Site favicon removed.');
        // Refresh component
        return redirect()->route('admin.cms.web-settings.index');
    }

    public function render()
    {
        return view('cms::livewire.web-settings.index');
    }
}
