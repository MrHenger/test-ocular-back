<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request) {
        // Validate credentials
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if(Auth::attempt($credentials)) {
            $token = Auth::user()->createToken('JWT')->plainTextToken;

            return response()->json([
                'data' => Auth::user(),
                'token' => $token,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Las credenciales ingresadas no coinciden con nuestros registros'
            ], 400);
        }
    }
}
