<?php

namespace App\Http\Controllers\Web\Manager;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Manager\Request;
use App\Models\Booking;
use App\Models\Building;
use App\Models\News;
use App\Models\User;


class ManagerController extends Controller
{
    public function dashboard()
    {
        $newsList = News::orderBy('created_at', 'desc')->take(5)->get();
        $buildings = Building::all();
        $users = User::all();
        $requests = Booking::where('status', 'pending')->get();
        return view('manager.dashboard', compact('users', 'newsList', 'requests','buildings'));
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

        return view('manager.dashboard', [
            'users' => $users,
        ]);
    }
}
