<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryWithFoods extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->name,
            'foods' => $this->foods
        ];
    }
}
