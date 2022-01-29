<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request) {
        // Validate credentials
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ], [], [
            'email' => 'correo',
            'password' => 'contraseña',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //Authenticate
        if(Auth::attempt($validator->validated())) {
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
