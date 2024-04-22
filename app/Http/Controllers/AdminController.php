<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function getUsers()
    {
        $users = User::leftJoin('roles', 'users.role_id', '=', 'roles.id')
        ->select('users.name', 'users.email', 'roles.type')
        ->get();
                     
        if ($users->isEmpty()) {
            return response()->json(['status' => 200, 'users' => []]);
                    }
    
        return response()->json(['status'=>200,'users' => $users]);
    }

    public function getUserById($userId)
    {
        // Fetch the user with the provided ID along with their role from the database
        $user = User::leftJoin('roles', 'users.role_id', '=', 'roles.id')
                     ->where('users.id', $userId)
                     ->select('users.name', 'users.email', 'roles.type')
                     ->first();
    
        if (!$user) {
            return response()->json(['status'=>200,'error' => 'User not found']);
        }
    
        return response()->json(['status'=>200,'user' => $user]);
    }

    public function changeUserInfo(Request $request, $userId)
{
    
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|min:3|max:255',
        'email' => 'required|email|unique:users,email,' . $userId,
    ]);

    if($validator->fails()){
        return response()->json([
            'status'=> 200,
    'message'=> $validator->messages(),
        ]);
    }else{

    
    $user = User::find($userId);

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    $user->name = $request->input('name');
    $user->email = $request->input('email');
    $user->save();

    return response()->json(['status'=>200,'message' => 'User updated successfully']);
}
}
   
public function deleteUserById($userId)
{
    $user = User::leftJoin('roles', 'users.role_id', '=', 'roles.id')
    ->where('users.id', $userId)
    ->select('users.name', 'users.email', 'roles.type')
    ->first();
                     
    if (!$user) {
        return response()->json(['status'=>200,'error' => 'User not found']);
        }

    $user->delete();

    return response()->json(['status'=>200,'message' => 'User deleted successfully']);
}

}