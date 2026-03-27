<?php

namespace App\Http\Controllers;

use App\Models\Clock;
use App\Services\ClockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClockController extends Controller
{
    public function __construct(private ClockService $clockService) {}

    public function index()
    {
        $dateInterval = [
            'from' => Carbon::parse(now())->format('Y-m-d 00:00:00'),
            'to' => Carbon::parse(now())->format('Y-m-d 23:59:59'),
        ];

        $todaySessions = $this->clockService->todaySessions();
        $metrics = $this->clockService->metrics($dateInterval);

        return view('index', compact('metrics', 'todaySessions'));
    }

    public function check(Request $request)
    {
        $this->clockService->check();

        return redirect()->back();
    }
}
