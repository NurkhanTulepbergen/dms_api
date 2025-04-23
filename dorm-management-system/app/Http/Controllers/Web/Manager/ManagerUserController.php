<?php

namespace App\Http\Controllers\Web\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagerUserController extends Controller
{
    public function create()
    {
        return view('manager.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:student,manager,admin'
        ]);

        User::create([
            'name' => $request->name,
            'user_id' => $request->user_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);


        return redirect()->route('manager.dashboard')
            ->with('successType', 'user_created')
            ->with('success', 'Пользователь создан!');
    }

    public function index()
    {
        $users = User::all();
        return view('manager.dashboard', compact('users'));
    }
}
