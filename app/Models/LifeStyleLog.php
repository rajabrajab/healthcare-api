<?php

namespace App\Models;
use Carbon\Carbon;

class LifeStyleLog extends BaseModel
{
    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($log){
            $date = Carbon::parse($log->taken_at)->toDateString();
            Gdf15Tracking::updateEffect($log->user_id, $date, $log->total_gdf15_effect,'lifestyle');
        });
    }

    public function lifeStyle()
    {
        return $this->belongsTo(LifeStyleBehavior::class,'life_style_behavior_id');
    }
}
