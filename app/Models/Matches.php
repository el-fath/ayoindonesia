<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Matches extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected $appends = ['score', 'status', 'statistic'];

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away');
    }

    public function details()
    {
        return $this->hasMany(MatchDetails::class);
    }

    public function getScoreAttribute()
    {
        $homeScore = $this->details->where('type', 1)->where('team', 'home')->count();
        $awayScore = $this->details->where('type', 1)->where('team', 'away')->count();

        return [
            'home' => $homeScore,
            'away' => $awayScore
        ];
    }

    public function getStatusAttribute()
    {
        if ($this->score['home'] == $this->score['away']) return "Draw";
        if ($this->score['home'] > $this->score['away']) return "Home Team Winner";
        if ($this->score['home'] < $this->score['away']) return "Away Team Winner";
    }

    public function getDoneAttribute()
    {
        if ($this->details->count() > 0) return true;
        return false;
    }

    public function getStatisticAttribute()
    {
        $home = $this->details->where('type', 1)->where('team', 'home')->pluck('player_id')->unique();
        $away = $this->details->where('type', 1)->where('team', 'away')->pluck('player_id')->unique();

        $homeStars = [];
        $homeNo = 0;
        foreach ($home as $star) {
            $homeStars[$homeNo]['player_id'] = $star;
            $homeStars[$homeNo]['goals'] = $this->details->where('type', 1)->where('player_id', $star)->count();
            $homeStars[$homeNo]['player'] = $this->details->where('type', 1)->where('player_id', $star)->first()->player;
            $homeNo++;
        }

        $awayStars = [];
        $awayNo = 0;
        foreach ($away as $star) {
            $awayStars[$awayNo]['player_id'] = $star;
            $awayStars[$awayNo]['goals'] = $this->details->where('type', 1)->where('player_id', $star)->count();
            $homeStars[$homeNo]['player'] = $this->details->where('type', 1)->where('player_id', $star)->first()->player;
            $awayNo++;
        }

        $data = array_merge($homeStars, $awayStars);
        $collection = collect($data);

        $highest = $collection->sortByDesc('goals')->first();
        $topScorer = $collection->where('goals', $highest['goals'])->all();

        return [
            'home_scorer' => $homeStars,
            'away_scorer' => $awayStars,
            'top_scorer'  => $topScorer
        ];
    }

    public static function boot() {
        
        parent::boot();

        static::deleting(function($data) {

            $data->details()->delete();

        });
    }
    
}
