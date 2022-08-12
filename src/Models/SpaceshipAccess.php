<?php

namespace LuisaeDev\Spaceship\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class SpaceshipAccess extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function space()
    {
        return $this->belongsTo(SpaceshipSpace::class, 'spaceship_space_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(SpaceshipRole::class, 'spaceship_role_id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }
}
