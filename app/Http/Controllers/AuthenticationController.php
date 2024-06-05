<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Cookie;

class AuthenticationController extends Controller
{
    public function user(){
        return Auth::user();
    }

    public function all(){
        return User::all();
    }

    public function register(Request $request){
        $user =  User::create(['name'=> $request->input(key: 'name'),
                            'email'=> $request->input(key: 'email'),
                            // 'phone'=> $request->input(key: 'phone'),
                            'role'=> $request->input(key: 'role'),
                            'password'=> Hash::make($request->input(key: 'password'))
                            ]);
                            

        $this->login($request);
    }

    public function login(Request $request){
        if(!Auth::attempt(['email'=> $request->input(key: 'email') , 'password'=> $request->input(key: 'password')])){
            return response()->json(['message'=> 'Invalid credentials'], 401);
        }else{
            $user = Auth::user();

            $token = $user->createToken('token')->plainTextToken;
            $cookie = cookie('jwt', $token, 60 * 24); // 1 day

            return response()->json(['message'=> 'Success', 'user'=> User::where('id', $user->id)->first(), 'token'=> $cookie], 200)->withCookie($cookie);
        }
    }

    public function logout(Request $request){
        $cookie = Cookie::forget('jwt');
        return response()->json(['message'=> 'Success'], 200)->withCookie($cookie);

    }

    // public function updateAccount(Request $request){
    //     User::find(Auth::user()->id)->update($request->except('password'));
    //     User::find(Auth::user()->id)->update(["password"=> Hash::make($request->password)]);
    //     return response()->json(User::with("worker")->where("id", Auth::user()->id)->first());

    // }
}
