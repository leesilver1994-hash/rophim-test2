<?php

namespace App\Services;

use Carbon\CarbonImmutable;

class BaziService
{
    private const STEMS = ['Jia', 'Yi', 'Bing', 'Ding', 'Wu', 'Ji', 'Geng', 'Xin', 'Ren', 'Gui'];
    private const BRANCHES = ['Zi', 'Chou', 'Yin', 'Mao', 'Chen', 'Si', 'Wu', 'Wei', 'Shen', 'You', 'Xu', 'Hai'];

    /**
     * Initial deterministic BaZi-like placeholder.
     * This is a migration bridge until the full calculation engine is ported.
     */
    public function calculate(array $payload): array
    {
        $date = CarbonImmutable::parse($payload['birth_date'].' '.$payload['birth_time']);

        $yearIndex = (($date->year - 4) % 60 + 60) % 60;
        $monthIndex = (($date->month + $yearIndex) % 60 + 60) % 60;
        $dayIndex = (($date->dayOfYear + $yearIndex) % 60 + 60) % 60;
        $hourIndex = ((int) floor($date->hour / 2) + $dayIndex) % 60;

        return [
            'pillars' => [
                'year' => $this->pillar($yearIndex),
                'month' => $this->pillar($monthIndex),
                'day' => $this->pillar($dayIndex),
                'hour' => $this->pillar($hourIndex),
            ],
            'meta' => [
                'timezone' => $payload['timezone'] ?? 'UTC',
                'gender' => $payload['gender'] ?? 'other',
                'engine' => 'laravel-port-bridge',
            ],
        ];
    }

    private function pillar(int $idx): string
    {
        return self::STEMS[$idx % 10].'-'.self::BRANCHES[$idx % 12];
    }
}
