<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /**
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
       
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password'=> $request->password,
            'birthday' => $request->birthday,
        ]);

        
        $token = $user->createToken('Personal Access Token', ['*'], now()->addHour())->plainTextToken;

        
        return response()->json([
            'message' => 'Регистрация успешна!',
            'token' => $token, 
            'user' => new UserResource($user),
        ], 201);
    }

    /**
     * 
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
   public function login(LoginRequest $request)
{
    if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
        $user = Auth::user();

        $maxTokens = 3;
        $tokens = $user->tokens;
        if ($tokens->count() >= $maxTokens) {
            $tokens->sortBy('created_at')
                ->take($tokens->count() - $maxTokens + 1)
                ->each->delete();
        }

        if ($user->is_2fa_enabled) {
            $tempToken = $user->createToken('2fa-token', ['2fa'])->plainTextToken;

            return response()->json([
                'message' => '2FA включена. Требуется подтверждение кода.',
                'token' => $tempToken,
                'requires_2fa' => true,
            ], 200);
        }

        // Если 2FA отключена — обычный токен
        $token = $user->createToken('auth-token', ['*'])->plainTextToken;

        return response()->json([
            'message' => 'Вход успешен',
            'token' => $token,
            'user' => new UserResource($user),
        ], 200);
    }

    return response()->json([
        'error' => 'Неавторизован',
        'message' => 'Неверные учетные данные',
    ], 422);
}

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'error' => 'Неавторизован',
            'message' => 'Вы не авторизованы.',
        ], 401);
    }

    return response()->json([
        'user' => new UserResource($user)
    ]);
}

    /**
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Выход из текущего сеанса.'
    ], 200);
}

    /*
     * @return \Illuminate\Http\JsonResponse
     */
    public function tokens()
    {
        $tokens = Auth::user()->tokens;
        return response()->json([
            'tokens' => $tokens
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logoutAll()
    {
        Auth::user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json([
            'message' => 'Все токены удалены из системы.'
        ], 200);
    }

    /**
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'error' => 'Неавторизован',
                'message' => 'Пароль некорректный.',
            ], 401);
        }

        $user->update(['password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'Смена пароля успешна.',
        ], 200);
    }
}