<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gdf15Tracking extends BaseModel
{
    protected $guarded = [];

    public static function updateEffect(int $userId, string $date, int $effect, string $type)
    {
        $tracking = self::firstOrNew([
            'user_id' => $userId,
            'tracking_date' => $date,
        ]);

        if ($type === 'food') {
            $tracking->total_food_effect += $effect;
        } elseif ($type === 'lifestyle') {
            $tracking->total_lifestyle_effect += $effect;
        }

        $tracking->total_gdf15_effect = $tracking->total_food_effect + $tracking->total_lifestyle_effect;
        $tracking->save();
    }
}
