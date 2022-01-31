<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Images;
use Illuminate\Http\Request;

class AuthMeController extends Controller
{
    public function authMe(Request $request) {
        $user = $request->user();
        $user['fullpath'] = asset('/images/'.(Images::find(1))->route);
        return response()->json($user, 200);
    }
}
