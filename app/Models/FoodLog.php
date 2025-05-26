<?php

namespace App\Models;
use Carbon\Carbon;

class FoodLog extends BaseModel
{
    protected $guarded = [];

    protected $casts = [
        'taken_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function getFormattedTimeAttribute(): string
    {
        return Carbon::parse($this->taken_at)->format('g:i A');
    }

    protected static function booted()
    {
        static::created(function ($log){
            $date = Carbon::parse($log->taken_at)->toDateString();
            Gdf15Tracking::updateEffect($log->user_id, $date, $log->total_gdf15_effect,'food');
        });
    }
}
