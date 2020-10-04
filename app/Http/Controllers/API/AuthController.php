<?php

namespace App\Http\Controllers\API;
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\JwtGuard;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
class AuthController extends Controller
{
    public function __construct()
    {
        $this->content = array();
    }
    //sign-up
    public function register(Request $request)
    {
        //validate
        $rules=[
            'name'=>'required|string',
            'email'=>['required','email','unique:users','regex:/^[a-zA-Z0-9]{0,}([.]?[a-zA-Z0-9]{1,})[@](gmail.com|hotmail.com|yahoo.com)$/'],
            'password'=>'required|min:8|confirmed'
        ];
        $validator=Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->content['status'] = 'error';
            $this->content['errors'] = $validator->errors()->all();
            return response()->json($this->content);
        }
        User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
        ]);
        $this->content['status'] = 'done';
        return response()->json($this->content, 201);
    }

    //Login user and create token
    public function login(Request $request)
    {
        $rules=[
        'email'=>['required','email'],
        'password'=>'required',
        'remember_me'=>'boolean'
    ];
        $validator=Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->content['status'] = 'error';
            $this->content['errors'] = $validator->errors()->all();
            return response()->json($this->content);
        }
        $credentials=$request->only('email', 'password');
        if (! $token=Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);


    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    public function refresh()
    {
        return $this->respondWithToken(Auth::guard()->refresh);
    }

    public function user()
    {
        $user=auth()->user();
        return response()->json($user);
    }


    public function logout()
    {
        Auth::guard()->logout;

        return response()->json(['status' => 'Successfully logged out'],);
    }


}




