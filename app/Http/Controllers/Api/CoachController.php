<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CoachController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Coach::rulesForNew());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if(Coach::where('name', '=', $request->name)->first()) {
            return response()->json([
                'error' => 'Coach name already exists'
            ], 422);
        }
        $club = Coach::create([
            'name' => $request->name,
            'email' => $request->email,
            'salary' => $request->salary
        ]);

        if(!$club) {
            return response()->json([
                'error' => 'There was a problem creating the coach'
            ], 500);
        }
        return response()->json([
            'status' => 'ok',
        ]);
    }
}
