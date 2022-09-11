<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'salary'
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public static function rulesForNew(): array {
        return [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'salary' => 'required',
        ];
    }

    public function hasClub(): bool
    {
        if($this->club()->exists()) {
            return true;
        }
        return false;
    }
}
