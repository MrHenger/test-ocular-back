<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthMeController extends Controller
{
    public function authMe(Request $request) {
        return response()->json($request->user(), 200);
    }
}
