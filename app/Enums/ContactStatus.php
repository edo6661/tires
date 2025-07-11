<?php
namespace App\Enums;

enum ContactStatus: string
{
    case PENDING = 'pending';
    case REPLIED = 'replied';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::REPLIED => 'Replied',
        };
    }
}
