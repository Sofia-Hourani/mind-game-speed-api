<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Question;
use App\Traits\Equations;
use Carbon\Carbon;
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
        $equation=$this->generateEquation($validated['level']);
        $game=Game::create([
            'player_name'=>$player_name,
            'level'=>$validated['level'],
        ]);

        Question::create(
            [
                'question' => $equation,
                'game_id' => $game->id,
                'answer' => eval('return '.$equation.';'),
                'asked_at' => now(),

            ]
        );
        return response()->json([
            'message'=>"Hello {$player_name}!,find your submit API URL below!",
            'submit_url'=>route('game.submit',$game->id),
            'question'=>$equation,
            'time_start'=>date('H:i:s'),
        ]);
    }
    public function submitGame($game_id )
    {

        $validated = \request()->validate([
            'answer' => 'required|numeric',
        ]);
        $question=Question::findOrFail($game_id)->latest()->first();
        if($validated['answer']==$question->answer){
            $message="Good job {$question->game->player_name}!}";
            $is_correct=true;
        }else{
            $message="Sorry {$question->game->player_name} is not a valid answer!";
            $is_correct=false;
        }
        $question->update([
            'is_correct'=>$is_correct,
            'answered_at'=>now(),
        ]);

        $time_in_second = Carbon::createFromFormat('H:i:s', $question->asked_at)
            ->diffInSeconds(Carbon::createFromFormat('Y-m-d H:i:s', $question->answered_at));

        $nextData = $this->generateEquation($question->game->level);
        $next = $question->create([
            'question' => $nextData,
             'game_id' => $game_id,
            'answer' => eval('return '.$nextData.';'),
            'asked_at' => now(),
        ]);

        $total = Question::whereNotNull('answered_at')->count();
        $correct = Question::where('is_correct', true)->count();
        $question->update([
            'score' => $correct/$total,
        ]);
        return response()->json([
            'message'=>$message,
            'is_correct'=>$is_correct,
            'time_taken'=>$time_in_second,
            'next_question'=>[
                'question' => $next->question,
                'submit_url'=>route('game.submit',$game_id),
            ],
            'current_score'=>$correct/$total,
            'end_game'=>route('game.end',$game_id),
        ]);
    }
    public function endGame()
    {

    }
}
