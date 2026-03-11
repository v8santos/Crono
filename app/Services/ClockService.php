<?php

namespace App\Services;

use App\Models\Clock;

class ClockService
{
    public function metrics(array $dateInterval): array
    {
        $now = now();

        $data = Clock::selectRaw("SUM(strftime('%s', IFNULL(clock_out, '{$now}')) - strftime('%s', clock_in)) / 60 as total_minutes, COUNT(id) as total_sessions")
            ->whereBetween('clock_in', $dateInterval)
            ->first();

        return [
            'hours' => floor($data->total_minutes / 60),
            'minutes' => $data->total_minutes % 60,
            'sessions_count' => $data->total_sessions,
        ];
    }
}