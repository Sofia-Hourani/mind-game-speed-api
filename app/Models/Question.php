<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'question','answer','game_id'
    ];

    public function game(){
        return $this->belongsTo(Game::class);
    }
}
