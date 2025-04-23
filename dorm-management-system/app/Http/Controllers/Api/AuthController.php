<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Регистрация нового пользователя
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'required|string|unique:users,user_id',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:admin,student,employee',
        ]);

        $user = User::create([
            'name' => $request->name,
            'user_id' => $request->user_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    /**
     * Вход в систему
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email', // Меняем user_id на email
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first(); // Ищем по email

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Неверный email или пароль.'], // Сообщение об ошибке
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    /**
     * Получение текущего пользователя
     */

    /**
     * Выход из системы (удаление токена)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Вы успешно вышли из системы.',
        ]);
    }

    public function setPassword(Request $request){
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed'
        ]);

        $user = $request->user();
        if(!Hash::check($request->old_password, $user->password)){
            return response()->json([
                'message' => 'Старый пароль введён неверно.'
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();
        return response()->json([
            'message' => 'Пароль успешно изменён.'
        ]);
    }
}
