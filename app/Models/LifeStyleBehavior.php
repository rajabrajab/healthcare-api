<?php

namespace App\Models;

class LifeStyleBehavior extends BaseModel
{
    protected $guarded = [];

    protected $casts = [
        'enum_values' => 'array'
    ];
}
