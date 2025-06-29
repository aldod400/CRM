<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;

class LanguageSwitcher extends Component
{
    public string $currentLocale;

    public function mount()
    {
        $this->currentLocale = App::getLocale();
    }

    public function switchLocale(string $locale)
    {
        if (array_key_exists($locale, config('app.available_locales'))) {
            session()->put('locale', $locale);
            $this->redirect(request()->header('Referer'));
        }
    }

    public function render()
    {
        return view('livewire.language-switcher', [
            'availableLocales' => config('app.available_locales'),
        ]);
    }
}
