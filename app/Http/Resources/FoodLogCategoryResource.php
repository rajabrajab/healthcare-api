<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodLogCategoryResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'category' => [
                'name' => $this['category']->name,
                'measurement_unit' => $this['category']->measurement_unit,
            ],
            'foods' => $this['logs']->map(function ($log) {
                return [
                    'id' => $log->food->id,
                    'name' => $log->food->name,
                    'description' => $log->food->description,
                    'quantity' => $log->quantity,
                ];
            }),
        ];
    }
}
