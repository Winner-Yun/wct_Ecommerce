<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Promote a user to admin
    public function promoteToAdmin($id)
    {
        if (auth()->user()->role !== 'super_admin') {
            return response()->json(['message' => 'Access Denied'], 403);
        }

        $user = User::findOrFail($id);
        $user->role = 'admin';
        $user->save();

        return response()->json(['message' => 'User promoted to admin successfully']);
    }

    // Demote admin to user
    public function demoteToUser($id)
    {
        if (auth()->user()->role !== 'super_admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user = User::findOrFail($id);

        if ($user->role !== 'admin') {
            return response()->json(['message' => 'User is not an admin'], 400);
        }

        $user->role = 'user';
        $user->save();

        return response()->json(['message' => 'Admin demoted to user']);
    }

    // Ban a user
    public function banUser($id)
    {
        if (auth()->user()->role !== 'super_admin') {
            return response()->json(['message' => 'Access Denied'], 403);
        }

        $user = User::findOrFail($id);
        $user->role = 'ban';
        $user->save();

        return response()->json(['message' => 'User has been banned']);
    }

    //unban
    public function unbanUser($id)
    {
        if (auth()->user()->role !== 'super_admin') {
            return response()->json(['message' => 'Access Denied'], 403);
        }

        $user = User::findOrFail($id);

        if ($user->role !== 'ban') {
            return response()->json(['message' => 'User is not banned'], 400);
        }

        $user->role = 'user'; // Or retrieve previous role from somewhere if needed
        $user->save();

        return response()->json(['message' => 'User has been unbanned']);
    }


    // Create a new admin
    public function createAdmin(Request $request){

        $user = auth()->user();

        if (!$user || $user->role !== 'super_admin') {
            return response()->json(['message' => 'Access Denied'], 403);
        }
        else{
            
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        return response()->json(['message' => 'Admin created successfully', 'admin' => $admin]);
    }

    public function index()
    {
        $users = User::all();

        return response()->json($users);
    }

}



