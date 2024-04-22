<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getUser()
    {
        $user = Auth::user();

        if ($user) {
            return response()->json(['status'=>200,
                'name' => $user->name,
                'email' => $user->email,
            ]);
        } else {
            return response()->json(['status'=>200,'error' => 'Unauthorized']);
        }
    }

    public function updateUser(Request $request)
    {
      
        $validator = Validator::make($request->all(), [
            'name'=>'required|string|min:3',
            'email'=>'required|email|unique:users,email,' . Auth::id(),
            ]);
           
             if($validator->fails()){
                 return response()->json([
                     'status'=> 200,
             'message'=> $validator->messages(),
                 ]);
             }else{

        $user = Auth::user();

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->save();

        return response()->json(['status'=>200,'message' => 'User updated successfully']);
    }
    }
   
}