<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Question;
use App\Traits\Equations;
use Carbon\Carbon;

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
            'your_result'=>$validated['answer'],
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
            'time_taken'=>$time_in_second
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
    public function endGame($game_id )
    {
        $questions=Question::all();
        $player_name=Question::findOrFail($game_id)->game->player_name;
        $level=Question::findOrFail($game_id)->game->level;
        $history=[];
        $all_questions=[];
        $all_answer=[];
        $all_time_taken=[];
        $best_score=[];
        foreach($questions as $question){
            if($question->is_correct==true && !empty(($question->time_taken))){
                $best_score[]=["spend time"=>[$question->time_taken],
                   "data"=> ["Question: ".$question->question." Answer:" .$question->answer ." Score:" .$question->score  ]] ;
            }
            $all_questions[]=$question->question;
            $all_answer[]=$question->answer;
            $all_time_taken[]=$question->time_taken;
            $history[]="Question #{$question->id}  ".$question->question." Correct Answer ".$question->answer ." Your Answer ".$question->your_result." spend time ".$question->time_taken;
        }
        $min_time = null;
        $score = [];
        foreach ($best_score as $entry) {
            $time = (int) $entry['spend time'][0];
            if (is_null($min_time) || $time < $min_time) {
                $min_time = $time;
                $score = [$entry];
            }
        }
        return response()->json([
            'player_name'=>$player_name,
            'level'=>$level,
            'total_time'=>$question->sum('time_taken'),
            'best_score'=>[
                'best_score'=>$score,
                'all_correct_score'=>$best_score
            ],
            'history'=>[
                'history'=>$history,
                'question'=>$all_questions,
                'answer'=>$all_answer,
                'response time'=>$all_time_taken,
            ]
        ]);
    }
}
