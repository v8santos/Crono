<?php

namespace App\Services;

use App\Models\Clock;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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

    public function todaySessions(): Collection
    {
        return Clock::whereBetween('clock_in', [now()->startOfDay(), now()->endOfDay()])->get();
    }

    public function check(): Model
    {
        $openSession = Clock::whereNull('clock_out')->first();

        if ($openSession) {
            $openSession->update(['clock_out' => now()]);

            return $openSession;
        }

        $currentSession = Clock::create(['clock_in' => now()]);

        return $currentSession;
    }
}