<?php

namespace App\Livewire\Customer\Auth;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Modules\CRM\Models\Customer;
use Modules\Global\Models\Country;

class Register extends Component
{
    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $phone_code = '';

    public string $phone = '';

    public string $password = '';

    public string $password_confirmation = '';

    #[Computed]
    public function countries()
    {
        return Country::where('status', 'active')
            ->orderBy('name')
            ->get(['name', 'iso2', 'phone_code']);
    }

    public function register(): void
    {
        $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone_code' => ['required', 'string', 'max:10'],
            'phone'      => [
                'required', 'string', 'max:20',
                Rule::unique('users', 'phone')->where('phone_code', $this->phone_code),
            ],
            'password'   => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $customer = Customer::updateOrCreate(
            [
                'email'      => $this->email,
                'phone_code' => $this->phone_code,
                'phone'      => $this->phone,
            ],
            [
                'name' => trim($this->first_name.' '.$this->last_name),
            ]
        );

        $user = User::create([
            'first_name'    => $this->first_name,
            'last_name'     => $this->last_name,
            'email'         => $this->email,
            'phone_code'    => $this->phone_code,
            'phone'         => $this->phone,
            'password'      => Hash::make($this->password),
            'type'          => UserType::Customer,
            'userable_type' => Customer::class,
            'userable_id'   => $customer->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        $this->redirect(route('customer.dashboard', absolute: false), navigate: true);
    }

    public function render()
    {
        return view(theme_view('livewire.auth.register'), [
            'countries' => $this->countries,
        ])->layout(theme_view('layouts.guest'));
    }
}
