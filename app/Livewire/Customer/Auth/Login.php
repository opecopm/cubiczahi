<?php

namespace App\Livewire\Customer\Auth;

use App\Livewire\Concerns\RedirectsAfterLogin;
use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Login extends Component
{
    use RedirectsAfterLogin;

    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $complete = $this->form->authenticate();

        if (! $complete) {
            $this->redirect(route('customer.mfa.challenge'), navigate: true);
            return;
        }

        Session::regenerate();

        $this->redirect($this->loginRedirectUrl(Auth::user()), navigate: true);
    }

    public function render()
    {
        return view(theme_view('livewire.auth.login'))
            ->layout(theme_view('layouts.guest'));
    }
}
