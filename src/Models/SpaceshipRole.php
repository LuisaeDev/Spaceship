<?php

namespace LuisaeDev\Spaceship\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpaceshipRole extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $guarded = [];

    public function acceses()
    {
        return $this->hasMany(SpaceshipAccess::class);
    }
}
