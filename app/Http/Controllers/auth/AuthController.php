<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\ResponseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
class AuthController extends ResponseController
{
    public function login(Request $request){
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();
        if($user){
            if(Hash::check($user->password, $credentials['password'])){
                if(Auth::attempt($credentials)){
                    $token = $user->createToken('auth-token')->plainTextToken;
                    return $this->success(
                        'Login successful', 
                            [
                                'token' => $token, 
                                'user' => $user
                            ]
                        );
                }
            }else{
                return $this->error('Invalid password', 401);
            }
        }else{
            return $this->error('User not found', 404);
        }
    }
    
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return $this->success('Logout successful');
    }

    public function register(Request $request){
        $inputs = $request->validated([
            'firstname' => 'required|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'avatar' => 'nullable|string|max:255',
            'sex' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
        ]);

        if($request->hasFile('avatar')){
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('avatars'), $avatarName);
            $inputs['avatar'] = $avatarName;
        }

        $user = User::create([
            'firstname' => $inputs['firstname'],
            'lastname' => $inputs['lastname'],
            'avatar' => $inputs['avatar'],
            'sex' => $inputs['sex'],
            'address' => $inputs['address'],
            'phone' => $inputs['phone'],
            'email' => $inputs['email'],
            'password' => Hash::make($inputs['password']),
            'city' => $inputs['city'],
            'country' => $inputs['country'],
            'role' => $inputs['role'] ?? 'user',
        ]);
        $token = $user->createToken('auth-token')->plainTextToken;
        return $this->success(
            'Register successful', 
                [
                    'token' => $token, 
                    'user' => $user
                ]
            );
    }
}
