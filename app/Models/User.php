<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function userDiet()
    {
        return $this->belongsToMany(Food::class, 'user_diets');
    }

    public function userFoodLogs()
    {
        return $this->hasMany(FoodLog::class);
    }

    public function userLifeStyleLogs()
    {
        return $this->hasMany(LifeStyleLog::class);
    }
}
