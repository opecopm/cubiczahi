<?php

namespace App\Enums;

enum UserType: string
{
    case Backend = 'backend';
    case Customer = 'customer';
    case Driver = 'driver';

    public function label(): string
    {
        return match ($this) {
            UserType::Backend => 'Backend',
            UserType::Customer => 'Customer',
            UserType::Driver => 'Driver',
        };
    }
}
