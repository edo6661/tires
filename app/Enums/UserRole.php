<?php
namespace App\Enums;

enum UserRole: string
{
    case CUSTOMER = 'customer';
    case ADMIN = 'admin';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::CUSTOMER => 'Customer',
            self::ADMIN => 'Admin',
        };
    }
}