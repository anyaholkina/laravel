<?php

namespace App\Http\Controllers;

use App\Models\TwoFactorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use App\Http\Resources\UserResource;

class TwoFactorAuthController extends Controller
{
    public function requestCode(Request $request)
    {
        Log::info('[2FA] Запрос на генерацию кода получен');

        $user = Auth::user();
        if (!$user) {
            Log::error('[2FA] Пользователь не авторизован');
            return response()->json(['message' => 'Пользователь не авторизован'], 401);
        }

        $clientId = $this->getClientIdentifier($request);
        $now = now();
        $code = random_int(100000, 999999);
        $ttl = config('auth.2fa_ttl', 300); 

        $record = TwoFactorCode::where('user_id', $user->id)
            ->where('client_identifier', $clientId)
            ->first();

        if ($record) {
            $record->request_count++;

            if ($record->request_count > 5) {
                sleep(50);
            } elseif ($record->request_count > 3) {
                sleep(30);
            }

            $record->code = $code;
            $record->expires_at = $now->copy()->addSeconds($ttl);
            $record->save();

            Log::info('[2FA] Обновлена существующая запись', $record->toArray());
        } else {
            try {
                $record = TwoFactorCode::create([
                    'user_id' => $user->id,
                    'client_identifier' => $clientId,
                    'code' => $code,
                    'expires_at' => $now->copy()->addSeconds($ttl),
                    'request_count' => 1,
                ]);
                Log::info('[2FA] Создана новая запись', $record->toArray());
            } catch (\Exception $e) {
                Log::error('[2FA] Ошибка при создании записи: ' . $e->getMessage());
                return response()->json(['message' => 'Ошибка при создании кода'], 500);
            }
        }

        return response()->json(['message' => 'Код сгенерирован', 'code' => $code]);
    }

    public function verifyCode(Request $request)
    {
        Log::info('[2FA] Запрос на проверку кода', $request->all());

        $request->validate(['code' => 'required|string']);

        $user = Auth::user();
        if (!$user) {
            Log::error('[2FA] Пользователь не авторизован при проверке кода');
            return response()->json(['message' => 'Пользователь не авторизован'], 401);
        }

        $clientId = $this->getClientIdentifier($request);

        $record = TwoFactorCode::where('user_id', $user->id)
            ->where('client_identifier', $clientId)
            ->first();

        if (
            !$record ||
            $record->code !== $request->code ||
            now()->greaterThan($record->expires_at)
        ) {
            Log::warning('[2FA] Неверный или просроченный код', [
                'input_code' => $request->code,
                'expected_code' => $record->code ?? null,
                'expires_at' => $record->expires_at ?? null,
            ]);
            return response()->json(['message' => 'Неверный или просроченный код'], 403);
        }

        $record->delete();
        $request->user()->currentAccessToken()->delete();

        $fullToken = $user->createToken('auth-token', ['*'])->plainTextToken;

        Log::info('[2FA] Код подтверждён, доступ разрешён');

        return response()->json([
            'message' => 'Код подтверждён. Доступ разрешён.',
            'token' => $fullToken,
            'user' => new UserResource($user),
        ]);
    }

    public function toggle2FA(Request $request)
    {
        Log::info('[2FA] Включение/отключение 2FA', $request->all());

        $request->validate([
            'enable' => 'required|boolean',
            'password' => 'required|string',
            'code' => 'nullable|string',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->password, $user->password)) {
            Log::warning('[2FA] Неверный пароль при попытке включения/отключения 2FA');
            return response()->json(['message' => 'Неверный пароль'], 403);
        }

        if (!$request->enable) {
            $clientId = $this->getClientIdentifier($request);
            $record = TwoFactorCode::where('user_id', $user->id)
                ->where('client_identifier', $clientId)
                ->first();

            if (
                !$record ||
                $record->code !== $request->code ||
                now()->greaterThan($record->expires_at)
            ) {
                Log::warning('[2FA] Неверный или просроченный код при отключении');
                return response()->json(['message' => 'Неверный или просроченный код'], 403);
            }

            $record->delete();
        }

        $user->is_2fa_enabled = $request->enable;
        $user->save();

        Log::info('[2FA] Статус 2FA изменён', ['enabled' => $request->enable]);

        return response()->json(['message' => $request->enable ? '2FA включена' : '2FA отключена']);
    }

    protected function getClientIdentifier(Request $request)
    {
        return hash('sha256', $request->ip() . '|' . $request->header('User-Agent'));
    }
}