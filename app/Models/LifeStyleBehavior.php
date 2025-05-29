<?php

namespace App\Models;

class LifeStyleBehavior extends BaseModel
{
    protected $guarded = [];

    protected $casts = [
        'enum_values' => 'array'
    ];

    protected $attributes = [
        'enum_values' => '[]'
    ];

    public function initializeEnumValues()
    {
        if (empty($this->enum_values)) {
            $this->enum_values = [];
        } elseif (is_string($this->enum_values)) {
            $this->enum_values = json_decode($this->enum_values, true);
        }
        return $this;
    }
}
