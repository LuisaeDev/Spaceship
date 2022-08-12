<?php

namespace LuisaeDev\Spaceship\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpaceshipSpace extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function accesses()
    {
        return $this->hasMany(SpaceshipAccess::class);
    }
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (is_null($model->name)) {
                $model->name = uniqid();
            }
        });
    }

    protected function bindedData(): Attribute
    {
        return Attribute::make(
            get: function($value) {
                if (is_string($value)) {
                    return json_decode($value, true);
                } else {
                    return null;
                }
            },
            set: function($value) {
                if (is_array($value)) {
                    return json_encode($value);
                } else {
                    return $value;
                }
            }
        );
    }

}
