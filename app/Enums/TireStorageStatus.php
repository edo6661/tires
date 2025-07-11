<?php
namespace App\Enums;

enum TireStorageStatus: string
{
    case ACTIVE = 'active';
    case ENDED = 'ended';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::ENDED => 'Ended',
        };
    }
}
