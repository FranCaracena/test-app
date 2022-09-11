<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'budget'
    ];

    public static function rulesForNew(): array {
        return [
            'name' => 'required|min:3',
            'budget' => 'required',
        ];
    }

    public static function rulesForCoach(): array {
        return [
            'club_id' => 'required|exists:clubs,id',
            'coach_id' => 'required|exists:coaches,id',
        ];
    }

    public static function rulesForPlayer(): array {
        return [
            'club_id' => 'required|exists:clubs,id',
            'player_id' => 'required|exists:players,id',
        ];
    }

    public static function rulesToRemovePlayer(): array {
        return [
            'club_id' => 'required|exists:clubs,id',
            'player_id' => 'required|exists:players,id'
        ];
    }

    public static function rulesToRemoveCoach(): array {
        return [
            'club_id' => 'required|exists:clubs,id',
        ];
    }

    public static function rulesForBudget(): array {
        return [
            'club_id' => 'required|exists:clubs,id',
            'budget' => 'required|integer',
        ];
    }

    public static function rulesToList(): array {
        return [
            'club_id' => 'required|exists:clubs,id',
            'id' => 'integer',
        ];
    }

    public function coach()
    {
        return $this->hasOne(Coach::class);
    }

    public function player()
    {
        return $this->hasMany(Player::class);
    }

    public function getRemainingBudgetAttribute()
    {
        $coach_salary = $this->coach->salary ?? 0;
        $players_salary = 0;
        if(count($this->player) > 0) {
            foreach ($this->player as $player) {
                $players_salary += $player->salary;
            }
        }
        return ($this->budget - $coach_salary - $players_salary);
    }

    public function getAllocatedBudgetAttribute()
    {
        return $this->budget - $this->remaining_budget;
    }

    public function hasCoach(): bool
    {
        if($this->coach()->exists()) {
            return true;
        }
        return false;
    }
}
