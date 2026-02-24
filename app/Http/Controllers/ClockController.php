<?php

namespace App\Http\Controllers;

use App\Models\Clock;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClockController extends Controller
{
    public function index(Request $request)
    {
        $data = Clock::where('clock_in', '>=', today())
            ->orWhereNull('clock_out')
            ->orderBy('created_at','desc')
            ->get();

        return response()->json($data);
    }

    public function check(Request $request)
    {
        $openSession = Clock::whereNull('clock_out')->first();

        if ($openSession) {
            $openSession->update(['clock_out' => now()]);

            return response()->json(['closedSession' => $openSession]);
        }

        $currentSession = Clock::create(['clock_in' => now()]);

        return response()->json(compact('currentSession'));
    }

    public function metrics(Request $request)
    {
        $dateInterval = [
            'from' => Carbon::parse($request->input('date_from', now()))->format('Y-m-d 00:00:00'),
            'to' => Carbon::parse($request->input('date_to', now()))->format('Y-m-d 23:59:59'),
        ];

        $now = now();

        $data = Clock::selectRaw("SUM(strftime('%s', IFNULL(clock_out, '{$now}')) - strftime('%s', clock_in)) / 60 as total_minutes, COUNT(id) as total_sessions")
            ->whereBetween('clock_in', $dateInterval)
            ->first();

        $hours = floor($data->total_minutes / 60);
        $minutes = $data->total_minutes % 60;

        return response()->json([
            'total_time' => "{$hours}h {$minutes}m",
            'total_sessions' => $data->total_sessions,
        ]);
    }
}
