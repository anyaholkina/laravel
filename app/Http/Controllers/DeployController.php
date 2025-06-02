<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DeployController extends Controller
{
    public function deploy(Request $request)
    {
        Log::info('update');

        $expectedKey = env('DEPLOY_SECRET');
        $providedKey = $request->input('secret_key');

        Log::info('Expected DEPLOY_SECRET: ' . var_export($expectedKey, true));
        Log::info('Provided secret_key: ' . var_export($providedKey, true));

        if ($providedKey !== $expectedKey) {
            Log::warning('Invalid secret key provided!');
            return response()->json(['message' => 'Invalid secret key'], 403);
        }

        $lockFile = storage_path('app/deploy.lock');
        if (File::exists($lockFile)) {
            return response()->json(['message' => 'Обновление уже выполняется. Попробуйте позже.'], 429);
        }

        File::put($lockFile, now());

        try {
            $ip = $request->ip();
            $timestamp = now()->toDateTimeString();
            Log::info("Git deploy started at $timestamp from IP: $ip");

            $output = [];

            // Проверка незакоммиченных изменений
            exec('git status --porcelain', $statusOutput);
            if (!empty($statusOutput)) {
                Log::info("Есть незакоммиченные изменения, выполняется git stash");
                exec('git stash push -m "auto-deploy-backup" 2>&1', $stashOutput);
                Log::info("Git stash", $stashOutput);
                $output[] = 'Сделан git stash перед сбросом';
            }

            // Обновление
            exec('git checkout main 2>&1', $checkoutOutput);
            Log::info("Git checkout main", $checkoutOutput);
            $output[] = 'Checked out to main';

            exec('git reset --hard 2>&1', $resetOutput);
            Log::info("Git reset --hard", $resetOutput);
            $output[] = 'Reset changes';

            exec('git pull 2>&1', $pullOutput);
            Log::info("Git pull", $pullOutput);
            $output[] = 'Pulled latest changes';

            // Кеши
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('cache:clear');
            $output[] = 'Caches cleared';

            return response()->json([
                'message' => 'Обновление выполнено успешно',
                'details' => $output,
            ]);
        } finally {
            File::delete($lockFile);
        }
    }
}