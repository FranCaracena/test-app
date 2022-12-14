<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\RelationMail;
use App\Models\Club;
use App\Models\Coach;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ClubController extends Controller
{

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), Club::rulesForNew());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if(Club::where('name', '=', $request->name)->first()) {
            return response()->json([
                'error' => 'Club name already exists'
            ], 422);
        }
        $club = Club::create([
            'name' => $request->name,
            'budget' => $request->budget
        ]);

        if(!$club) {
            return response()->json([
                'error' => 'There was a problem creating the club'
            ], 500);
        }
        return response()->json([
            'status' => 'Club created',
        ]);
    }

    public function addCoach(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), Club::rulesForCoach());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $club = Club::find($request->club_id);
        $coach = Coach::find($request->coach_id);

        if($club->hasCoach()) {
            if($club->coach->id == $request->coach_id) {
                return response()->json([
                    'error' => 'The coach is already in the club'
                ], 422);
            }
            return response()->json([
                'error' => 'The club already has the coach ' . $club->coach->name
            ], 422);
        }elseif($coach->hasClub()) {
            return response()->json([
                'error' => 'The coach already has the club ' . $coach->club->name
            ], 422);
        }

        if($coach->salary > $club->remaining_budget)
            return response()->json([
                'error' => 'The coach salary is higher than the available budget'
            ], 422);

        $club->coach()->save($coach);

        Mail::to($coach->email)->send(new RelationMail('You have been added to the team ' . $club->name));

        return response()->json([
            'Coach added to Club'
        ]);
    }

    public function addPlayer(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), Club::rulesForPlayer());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $club = Club::find($request->club_id);
        $player = Player::find($request->player_id);

        if($player->hasClub()) {
            if($player->club->id == $request->club_id) {
                return response()->json([
                    'error' => 'The player is already in the club'
                ], 422);
            }
            return response()->json([
                'error' => 'The player already has the club ' . $player->club->name
            ], 422);
        }

        if($player->salary > $club->remaining_budget)
            return response()->json([
                'error' => 'The player salary is higher than the available budget'
            ], 422);

        $club->player()->save($player);

        Mail::to($player->email)->send(new RelationMail('You have been added to the team ' . $club->name));

        return response()->json([
            'Player added to Club'
        ]);
    }

    public function changeBudget(Request $request)
    {
        $validator = Validator::make($request->all(), Club::rulesForBudget());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $club = Club::find($request->club_id);

        if($request->budget < $club->allocated_budget) {
            return response()->json([
                'error' => 'The budget is lower than the current salaries to pay'
            ]);
        }

        $club->budget = $request->budget;
        $club->save();

        return response()->json([
            'Budget updated'
        ]);
    }

    public function removePlayer(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), Club::rulesToRemovePlayer());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $club = Club::find($request->club_id);
        $player = Player::find($request->player_id);
        if(!$player->hasClub()) {
            return response()->json([
                'error' => 'The player ' . $player->name . ' doesn`t have a club'
            ], 422);
        }elseif($player->club->id != $request->club_id) {
            return response()->json([
                'error' => 'The player ' . $player->name . ' have a different club'
            ], 422);
        }

        $player->club()->dissociate()->save();

        Mail::to($player->email)->send(new RelationMail('You have been removed from the team ' . $club->name));

        return response()->json([
            $player->name . ' removed from ' . $club->name
        ]);
    }

    public function removeCoach(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), Club::rulesToRemoveCoach());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $club = Club::find($request->club_id);
        $coach = $club->coach;
        if(!$club->hasCoach()) {
            return response()->json([
                'error' => 'The club ' . $club->name . ' doesn`t have a coach'
            ], 422);
        }

        $club->coach->club()->dissociate()->save();

        Mail::to($coach->email)->send(new RelationMail('You have been removed from the team ' . $club->name));

        return response()->json([
            'Coach removed from ' . $club->name
        ]);
    }

    public function listPlayers(Request $request): \Illuminate\Http\JsonResponse
    {

        $validator = Validator::make($request->all(), Club::rulesToList());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $playersQuery = Player::where('club_id', '=', $request->club_id);

        if($request->has('name')) {
            $playersQuery->where('name', 'like', '%'.$request->name.'%');
        }

        if($request->has('email')) {
            $playersQuery->where('email', 'like', '%'.$request->email.'%');
        }

        if($request->has('id')) {
            $playersQuery->where('id', '=', $request->id);
        }

        if($request->has('salary')) {
            preg_match('/[\<\>\=]/', $request->salary, $request_operator);
            $operator = $request_operator[0] ?? '=';
            $salary = preg_replace('/[\<\>\=]/', '', $request->salary);
            $playersQuery->where('salary', $operator, $salary);
        }

        return response()->json([$playersQuery->paginate(5)]);

    }
}
