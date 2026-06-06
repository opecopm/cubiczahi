<?php

namespace App\Livewire\Admin\Auth;

use App\Livewire\Concerns\RedirectsAfterLogin;
use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('admin.layouts.guest')]
class Login extends Component
{
    use RedirectsAfterLogin;

    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $complete = $this->form->authenticate();

        if (! $complete) {
            $this->redirect(route('admin.mfa.challenge'), navigate: true);
            return;
        }

        Session::regenerate();

        $this->redirect($this->loginRedirectUrl(Auth::user()), navigate: true);
    }

    public function render()
    {
        return view('admin.livewire.auth.login');
    }
}
