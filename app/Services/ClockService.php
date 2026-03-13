<?php

namespace App\Services;

use App\Models\Clock;

class ClockService
{
    public function metrics(array $dateInterval): array
    {
        $now = now();

        $data = Clock::selectRaw("SUM(TIMESTAMPDIFF(SECOND, clock_in, COALESCE(clock_out, '{$now}'))) / 60 AS total_minutes,
COUNT(id) AS total_sessions")
            ->whereBetween('clock_in', $dateInterval)
            ->first();

        return [
            'hours' => floor($data->total_minutes / 60),
            'minutes' => $data->total_minutes % 60,
            'sessions_count' => $data->total_sessions,
        ];
    }
}