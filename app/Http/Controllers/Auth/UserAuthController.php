<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{

    public function register(Request $request)
    {
        
            $validator = Validator::make($request->all(), [
           'name'=>'required|min:3|max:255',
           'email'=>'required|email|unique:users',
           'password'=>'min:6|required|confirmed',
           
           ]);

         
            if($validator->fails()){
                return response()->json([
                    'status'=> 200,
            'message'=> $validator->messages(),
                ]);
            }else{


             $user = User::create([
                 'email' => $request->input('email'),
                 'name' => $request->input('name'),
                 'password' => Hash::make($request->input('password')),
             ]);

             return response()->json(['status' => 200,
             'message'=>'Registration was successful']);

                 }
}
    
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'=>'required|email',
            'password'=>'required',
            
            ]);
           
             if($validator->fails()){
                 return response()->json([
                     'status'=> 200,
             'message'=> $validator->messages(),
                 ]);
             }else{
        $credentials = $request->only('email', 'password');

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'role_id' => 2])) {
            $user = Auth::user();
            $token = $user->createToken('UserManagement', ['user'])->accessToken;

            return response()->json(['status'=>200,'token' => $token]);
        } else {
            return response()->json(['status'=>200,'error' => 'Wrong login details'], );
        }}
    }


    public function logout()
    {
        $user = Auth::user();

        $user->token()->revoke();

        return response()->json(['message' => 'Successfully logged out']);
    }
}