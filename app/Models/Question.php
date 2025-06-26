<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = ['game_id', 'question', 'answer', 'asked_at', 'answered_at', 'is_correct','your_result','time_taken','score'];


    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
