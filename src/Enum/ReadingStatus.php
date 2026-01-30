<?php

namespace App\Enum;

enum ReadingStatus: string
{
    case ToRead = 'to_read';
    case Reading = 'reading';
    case Read = 'read';

    public function label(): string
    {
        return match ($this) {
            self::ToRead => 'À lire',
            self::Reading => 'En lecture',
            self::Read => 'Lu',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ToRead => 'lucide:bookmark',
            self::Reading => 'lucide:book-open',
            self::Read => 'lucide:check-circle',
        };
    }

    public function nextStatus(): self
    {
        return match ($this) {
            self::ToRead => self::Reading,
            self::Reading => self::Read,
            self::Read => self::ToRead,
        };
    }

    /**
     * Returns [backgroundColor, textColor] as hex values.
     */
    public function colors(): array
    {
        return match ($this) {
            self::ToRead => ['#dbeafe', '#1e40af'],
            self::Reading => ['#ffedd5', '#9a3412'],
            self::Read => ['#dcfce7', '#166534'],
        };
    }
}
