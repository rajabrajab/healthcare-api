<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class ReadingLog extends Model
{
    protected $guarded = [];

    public function getFormattedTimeAttribute(): string
    {
        return Carbon::parse($this->reading_time)->format('g:i A');
    }
}
