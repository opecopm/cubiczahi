<?php

namespace App\Livewire;

use Modules\Global\Models\Country;
use Modules\Global\Models\State;

trait WithCountryStateCityTrait
{
    public $countries = [];

    public $states = [];

    public $cities = [];

    public $country;

    public $state;

    public $city;

    public function initializeWithCountryStateCityTrait()
    {
        $this->countries = Country::all();
    }

    public function updatedCountry($value)
    {
        $country = Country::where('name', $value)->first();
        $this->states = $country ? $country->states : [];
        $this->state = null;
        $this->cities = [];
    }

    public function updatedState($value)
    {
        $state = State::where('name', $value)->first();
        $this->cities = $state ? $state->cities : [];
        $this->city = null;
    }
}
