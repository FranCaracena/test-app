<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Coach;
use App\Models\Player;
use Illuminate\Http\Request;
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

    public function addCoach($model, Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), Club::rulesForCoach());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $club = Club::find($request->club_id);
        $coach = Coach::find($request->coach_id);

        if($club->hasCoach()) {
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
            return response()->json([
                'error' => 'The player already has the club ' . $player->club->name
            ], 422);
        }

        if($player->salary > $club->remaining_budget)
            return response()->json([
                'error' => 'The player salary is higher than the available budget'
            ], 422);

        $club->player()->save($player);
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
        }

        return response()->json([
            $player->name . ' removed from ' . $club->name
        ]);
    }
}
