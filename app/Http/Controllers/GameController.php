<?php

namespace App\Http\Controllers;

use App\Traits\Equations;
use Illuminate\Http\Request;

class GameController extends Controller
{
    use Equations;
    public function startGame()
    {
        $player_name=request('player_name');
        $question=$this->generateEquation(\request('level'));
        return response()->json([
            'message'=>"Hello {$player_name}!,find your submit API URL below!",
            'submit_url'=>route('game.start'),
            'question'=>$question,
            'time_start'=>date('H:i:s'),
        ]);
    }
}
