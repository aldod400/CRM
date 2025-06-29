<?php

namespace App\Filament\Pages\Components;

use Filament\Pages\Page;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class LanguageSelector extends Component
{
    public function render()
    {
        return view('components.language-selector', [
            'currentLocale' => App::getLocale(),
            'availableLocales' => config('app.available_locales'),
        ]);
    }

    public function switchLocale($locale)
    {
        if (array_key_exists($locale, config('app.available_locales'))) {
            session()->put('locale', $locale);
            return redirect()->to(request()->header('Referer'));
        }
    }
}
