<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;
    protected $fillable = [
        'player_name','level'
    ];
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
