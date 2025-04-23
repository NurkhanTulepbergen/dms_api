<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\Manager;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminUserController extends Controller
{


    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:student,manager,admin,employee',
            'job_type' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'user_id' => $request->user_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        if ($user->role === 'student') {
            Student::create([
                'user_id' => $user->id,
                'student_id' => $user->id, // Если student_id = user_id
                'room_id' => null, // Пока нет комнаты, обновится позже
            ]);
        } elseif ($user->role === 'manager') {
            Manager::create([
                'user_id' => $user->id,
                'manager_id' => $user->id,
            ]);
        } elseif ($user->role === 'admin') {
            Admin::create([
                'user_id' => $user->id,
                'admin_id' => $user->id,
            ]);
        } elseif ($user->role === 'employee') {
            Employee::create([
                'user_id' => $user->id,
                'employee_id' => $user->id,
                'name' => $user->name,
                'job_type' => $request->job_type ?? 'Не указано',
            ]);
        }


        return redirect()->route('admin.dashboard')
            ->with('successType', 'user_created')
            ->with('success', 'Пользователь создан!');
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('filter_id')) {
            $query->where('user_id', 'like', '%'.$request->filter_id.'%');
        }
        if ($request->filled('filter_name')) {
            $query->where('name', 'like', '%'.$request->filter_name.'%');
        }
        if ($request->filled('filter_email')) {
            $query->where('email', 'like', '%'.$request->filter_email.'%');
        }
        if ($request->filled('filter_role')) {
            $query->where('role', 'like', '%'.$request->filter_role.'%');
        }

        $users = $query->get();

        return view('admin.dashboard', [
            'users' => $users,
            'newsList' => collect(), // empty collection to avoid the error
        ]);    }


    public function destroy($id)
    {
        Log::info("Deleting user with ID: $id");
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.dashboard')
            ->with('successType', 'user_deleted')
            ->with('success', 'Пользователь удалён!');
    }


    public function getUserJson($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Пользователь не найден'], 404);
        }
        return response()->json($user);
    }


}
