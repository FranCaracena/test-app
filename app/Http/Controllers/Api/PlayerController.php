<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlayerController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Player::rulesForNew());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if(Player::where('name', '=', $request->name)->first()) {
            return response()->json([
                'error' => 'Player name already exists'
            ], 422);
        }
        $club = Player::create([
            'name' => $request->name,
            'email' => $request->email,
            'salary' => $request->salary
        ]);

        if(!$club) {
            return response()->json([
                'error' => 'There was a problem creating the player'
            ], 500);
        }
        return response()->json([
            'status' => 'ok',
        ]);
    }
}
