<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserRegisteredNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid email or password',
            ], 401);
        }
        $user = auth()->user();
        $id = $user->id;
        $name = $user->name;
        $roles = $user->roles;
        $permissions = $roles->flatMap(function ($role) {
            return $role->permissions->pluck('name');
        });
        return response()->json([
            'access_token' => $token,
            "roles" => $roles,
            "name" => $name,
            'permissions' => $permissions,
            "id" => $id,
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ]);
    }


  

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'عملية تسجيل الخروج تمت بنجاح']);
    }
}
