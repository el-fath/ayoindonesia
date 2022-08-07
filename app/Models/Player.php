<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected $appends = ['goals'];

    public function positions()
    {
        return $this->belongsToMany(Position::class, 'player_positions');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function statistic()
    {
        return $this->hasMany(MatchDetails::class);
    }

    public function getGoalsAttribute()
    {
        return $this->statistic->where('type', 1)->count();
    }

    public static function boot() {
        
        parent::boot();

        static::deleting(function($data) {

            $data->positions()->detach();

        });
    }
}
