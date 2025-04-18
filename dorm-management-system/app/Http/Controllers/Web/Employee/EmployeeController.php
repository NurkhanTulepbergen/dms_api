<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Hash;
use App\Models\Employee;
use App\Models\News;
use App\Models\Request as RepairRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{

    public function dashboard(){
        $newsList = News::all();
        $user = request()->user();
        $repairRequests = RepairRequest::all();
        return view('employee.dashboard', compact('newsList', 'user','repairRequests'));
    }
    public function requests(){
        $repairRequests = RepairRequest::where('user_id', auth()->id())->get();
        return view('employee.requests', compact('repairRequests'));
    }
    public function show($id){
        $request = RepairRequest::with('employee')->findOrFail($id);
        return view('employee.show', compact('request'));

    }
    public function edit($id){
        $request = RepairRequest::findOrFail($id); // Ищем по ID

        return view('employee.edit', compact('request'));
    }
    public function update(Request $httpRequest, $id){
        $employee = Employee::where('user_id', auth()->id())->first();
        if (!$employee) {
            return redirect()->back()->with('error', 'Вы не зарегистрированы как сотрудник.');
        }
        $request = RepairRequest::where('id', $id)
            ->where('employee_id', $employee->id)
            ->first();
//        dd($request);

        if (!$request) {
            return redirect()->back()->with('error', 'Запрос не найден или у вас нет доступа.');
        }

        $request->update(['status' => $httpRequest->input('status')]);

        return redirect()->route('employee.requests')->with('success', 'Статус успешно обновлён.');

    }

    public function updateProfile(Request $request){
        $request->validate([
            'phone'=> 'nullable|string|max:20',
            'photo'=> 'nullable|image|max:2048',
            'job_type'=> 'nullable|string|max:20',
        ]);

        $user = Auth::user();

        if ($request->phone) {
            $user->phone = $request->phone;
        }

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('avatars', 'public');
            $user->photo = $path;
        }

        if ($request->job_type && $user->employee) {
            $user->employee->job_type = $request->job_type;
            $user->employee->save();
        }
        $user->save();
        return redirect('employee/dashboard')->with('successType', 'profile_updated');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'phone'                 => 'nullable|string|max:255',
            'photo'                 => 'nullable|image|max:2048',
            'current_password'      => 'required_with:new_password',
            'new_password'          => 'nullable|confirmed|min:6',
        ]);

        if ($request->filled('phone')) {
            $user->phone = $request->phone;
        }

        // Обновляем пароль, если передан new_password
        if ($request->filled('new_password')) {
            // Проверяем текущий пароль
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Текущий пароль введён неверно.']);
            }
            // Записываем новый
            $user->password = Hash::make($request->new_password);
        }

        // Обновляем фото
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $user->photo = $path;
        }

        $user->save();

        return back()->with('success', 'Данные успешно обновлены!');
    }
}
