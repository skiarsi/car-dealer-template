<?php

namespace App\Support;

class OpeningHours
{
    public const DAYS = [
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
    ];

    public static function defaults(): array
    {
        $hours = [];

        foreach (self::DAYS as $day) {
            $hours[$day] = [
                'closed' => $day === 'sunday',
                'open' => '09:00',
                'close' => $day === 'saturday' ? '14:00' : '18:00',
            ];
        }

        return $hours;
    }

    public static function normalize(?array $hours): array
    {
        $normalized = self::defaults();

        foreach (self::DAYS as $day) {
            $slot = $hours[$day] ?? [];
            $closed = (bool) ($slot['closed'] ?? $normalized[$day]['closed']);

            $normalized[$day] = [
                'closed' => $closed,
                'open' => self::time($slot['open'] ?? $normalized[$day]['open']),
                'close' => self::time($slot['close'] ?? $normalized[$day]['close']),
            ];
        }

        return $normalized;
    }

    public static function time(mixed $value): string
    {
        $value = (string) $value;

        if (preg_match('/^([01]?\d|2[0-3]):([0-5]\d)(?::[0-5]\d)?$/', $value, $matches) !== 1) {
            return '09:00';
        }

        return sprintf('%02d:%02d', $matches[1], $matches[2]);
    }
}
