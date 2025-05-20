<?php

namespace App\Models;

class Food extends BaseModel
{
    protected $guarded = [];

    protected $table = 'foods';

    public function category()
    {
        return $this->belongsTo(FoodCategory::class);
    }
}
