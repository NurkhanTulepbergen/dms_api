<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\News;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $newsList = News::orderBy('created_at', 'desc')->take(5)->get();
        $users = User::all();
        $requests = Booking::where('status', 'pending')->get();
        return view('admin.dashboard', compact('users', 'newsList', 'requests'));
    }
    public function index(Request $request)
    {
        $query = User::query();

        $users = $query->get();

        return view('admin.dashboard', [
            'users' => $users,
            // Если нужно, передаем ещё что-то (newsList, requests, etc.)
        ]);
    }
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function showJson($id)
    {
        $user = User::findOrFail($id);
        // Вернём JSON
        return response()->json($user);
    }



}
