<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            $username = $request->get('username');
            $password = $request->get('password');

            if (Auth::attempt(['username' => $username, 'password' => $password])) {
                $user = Auth::user();

                $data['user_id'] = $user->id;
                $data['full_name'] = $user->name;
                $data['email'] = $user->username;
                $data['token'] = "Bearer ". $user->createToken('pragti')->accessToken;

                return response()->json(['success' => true, 'message' => 'Login successfully.!!', 'data' => $data], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Invalid username or password..!'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage() . " In File " . $e->getFile() . " On Line " . $e->getLine()], 500);
        }
    }
}
