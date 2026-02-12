<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LocaleSwitcher extends Component
{
    public $locale;

    public function mount()
    {
        $this->locale = Auth::check() ? Auth::user()->locale ?? config('app.locale') : session('locale', config('app.locale'));
    }

    public function updatedLocale($value)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->locale = $value;
            $user->save();
        }

        session(['locale' => $value]);

        // reload to apply immediately
        $this->dispatchBrowserEvent('locale-changed');
    }

    public function render()
    {
        return view('livewire.locale-switcher', [
            'locales' => config('app.available_locales'),
        ]);
    }
}
