<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Question;
use App\Traits\Equations;
use Illuminate\Http\Request;

class GameController extends Controller
{
    use Equations;
    public function startGame()
    {
        $validated = request()->validate([
            'player_name'=>['required'],
            'level'=>['required','Integer','between:1,4'],
        ]);

        $player_name=$validated['player_name'];
        $question=$this->generateEquation($validated['level']);
        $game=Game::create([
            'player_name'=>$player_name,
            'level'=>$validated['level'],
        ]);

        $question=Question::create([
            'question'=>$question,
             'answer'=>eval('return '.$validated['level'].';'),
            'game_id'=>$game->id,
        ]);

        return response()->json([
            'message'=>"Hello {$player_name}!,find your submit API URL below!",
            'submit_url'=>route('game.submit',$game->id),
            'question'=>$question,
            'time_start'=>date('H:i:s'),
        ]);
    }
    public function submitGame($id)
    {
        $game = Game::findOrFail($id);
        $answer=request('answer');
        $question=$this->generateEquation(request('level'));
        $result =(float) eval('return ' . $game['question'] . ';');
        if($answer==$result){
          $message="Good job {$game->player_name}, your answer is correct!";
        }else{
            $message="Sorry {$game->player_name}, your answer is incorrect.";
        }
        $game->answer=$answer;


        return response()->json([
           'message'=>$message,
            'answer'=>$answer,
            'submit_url'=>route('game.submit',$question),
            'question'=>$game['question'],
        ]);

    }
}
