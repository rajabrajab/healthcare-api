<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteFoodCategoryResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'category' => [
                'name' => $this['category']->name,
                'measurement_unit' => $this['category']->measurement_unit,
            ],
            'foods' => $this['foods']->map(function ($food) {
                return [
                    'id' => $food->id,
                    'name' => $food->name,
                    'description' => $food->description,
                ];
            }),
        ];
    }
}
