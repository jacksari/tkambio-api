<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->responseJson([
                'errors' => $validator->errors()->all(),
            ], 'Error al registrar usuario', 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'password_literal' => $request->password,
        ]);

        $token = auth('api')->attempt($validator->validated());

        $user = $this->userAccount(auth('api')->user()->id);

        return $this->responseJson([
            'user' => $user,
            'access_token' => $token
        ], 'Usuario registrado correctamente.', 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->responseJson([], 'Error al iniciar sesión', 400);
        }

        if (!$token = auth('api')->attempt($validator->validated())) {
            return $this->responseJson([], 'Credenciales incorrectas', 401);
        }

        return $this->respondWithToken($token, auth('api')->user()->id);
    }

    public function profile()
    {
        $user = $this->userAccount(auth('api')->user()->id);
        return $this->responseJson($user, 'Perfil del usuario', 200);
    }

    public function logout()
    {
        auth()->logout();

        return $this->responseJson([], 'Sesión cerrada correctamente', 200);
    }

    public function refresh()
    {
        $user = $this->userAccount(auth('api')->user()->id);
        $token = auth('api')->refresh();
        return $this->respondWithToken($token, $user->id);
    }

    protected function respondWithToken($token, $user_id)
    {
        $user = $this->userAccount($user_id);
        return $this->responseJson([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user
        ], 'Credenciales del usuario', 200);
    }

    protected function userAccount($user_id)
    {
        $user = User::find($user_id);
        return $user;
    }
}
