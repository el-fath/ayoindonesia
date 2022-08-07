<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Team extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected $appends = ['logo_url', 'statistic'];

    public function getLogoUrlAttribute()
    {
        return Storage::url('files/' . $this->logo);
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function homeMatches()
    {
        return $this->hasMany(Matches::class, 'home');
    }

    public function awayMatches()
    {
        return $this->hasMany(Matches::class, 'away');
    }

    public function getMatchesAttribute()
    {
        $merged = $this->homeMatches->merge($this->awayMatches);

        return $merged->sortBy('id')->all();
    }

    public function getStatisticAttribute()
    {
        $countHomeWinner = $this->homeMatches->where('status', 'Home Team Winner')->where('done', true)->count();
        $countAwayWinner = $this->homeMatches->where('status', 'Away Team Winner')->where('done', true)->count();
        $draw = $this->homeMatches->where('status', 'Draw')->where('done', true)->count();

        $matches = $countHomeWinner + $countAwayWinner + $draw;
        $win = $countHomeWinner + $countAwayWinner;

        $PlayersGoals = $this->players->sortByDesc('goals');
        $PlayersScore = $PlayersGoals->all();
        $highest = $PlayersGoals->first()->goals ?? NULL;

        return [
            'matches' => $matches,
            'win' => $win,
            'lose' => $matches - $win - $draw,
            'draw' => $draw,
            'players_scorer' => $PlayersScore,
            'top_scorer' => $PlayersGoals->where('goals', $highest)->all()
        ];
    }
}
