<?php

namespace App\Models;

class FoodCategory extends BaseModel
{
    public function foods()
    {
        return $this->hasMany(Food::class, 'category_id');
    }
}
