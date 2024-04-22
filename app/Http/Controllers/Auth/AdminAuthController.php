<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    public function adminLogin(Request $request)
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

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'role_id' => 1])) {
            $user = Auth::user();
            $token = $user->createToken('UserManagement', ['admin'])->accessToken;

            return response()->json(['status'=>200,'token' => $token]);
        } else {
            return response()->json(['status'=>200,'error' => 'Unauthorized']);
        }}
    }


    public function adminLogout()
    {
        $user = Auth::user();

        $user->token()->revoke();

        return response()->json(['message' => 'Successfully logged out']);
    }
}