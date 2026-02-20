<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Clock extends Model
{
    protected $fillable = ['clock_in', 'clock_out'];

    protected function clockIn(): Attribute
    {
        return Attribute::make(
            fn (string $value) => Carbon::parse($value)->setTimezone('America/Sao_Paulo')->format('H:i d/m/Y'),
        );
    }

    protected function clockOut(): Attribute
    {
        return Attribute::make(
            fn (?string $value): ?string => empty($value) ? $value : Carbon::parse($value)->setTimezone('America/Sao_Paulo')->format('H:i d/m/Y'),
        );
    }
}
