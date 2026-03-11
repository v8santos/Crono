<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Clock;
use App\Services\ClockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClockController extends Controller
{
    public function __construct(private ClockService $clockService) {}

    public function index(Request $request)
    {
        $data = Clock::where('clock_in', '>=', today())
            ->orWhereNull('clock_out')
            ->orderBy('created_at','desc')
            ->get();

        return response()->json($data);
    }

    public function getAll(Request $request)
    {
        $data = Clock::orderBy('created_at','desc')
            ->get(); // Colocar paginação

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

    public function adjustment(Request $request)
    {
        $request->validate([
            'date_time' => [
                'required',
                Rule::date()->beforeOrEqual(now())
            ],
            'session_id' => [
                'sometimes',
                'integer'
            ],
        ]);

        // Garantindo que o date time seja BR
        // E depois convertido para o timezone do servidor
        $dateTime = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $request->input('date_time'),
            'America/Sao_Paulo'
        )->setTimezone(config('app.timezone'));

        $openSession = Clock::when($request->input('session_id') , function ($query, $value) {
            $query->where('id', $value);
        }, function ($query) {
            $query->whereNull('clock_out');
        })->first();

        if ($openSession) {
            $openSession->update(['clock_out' => $dateTime]);

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

        $metrics = $this->clockService->metrics($dateInterval);

        return response()->json([
            'total_time' => "{$metrics['hours']}h {$metrics['minutes']}m",
            'total_sessions' => $metrics['total_sessions'],
        ]);
    }
}
