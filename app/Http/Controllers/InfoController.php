<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ServerInfoResource;
use App\Http\Resources\ClientInfoResource;
use App\Http\Resources\DatabaseInfoResource;
use Illuminate\Support\Facades\DB;

class InfoController extends Controller
{
    public function server()
    {
        // Получаем только версию PHP
        $phpVersion = phpversion();

        // Возвращаем информацию в формате JSON
        return response()->json(new ServerInfoResource([
            'php_version' => $phpVersion,
        ]));
    }

    public function client(Request $request)
    {
        // Получаем IP-адрес клиента и его User-Agent
        return response()->json(new ClientInfoResource([
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]));
    }

    public function database()
    {
        try {
            // Попытка выполнить запрос для получения данных о базе данных
            $databaseInfo = DB::select('SELECT DATABASE() AS database_name');

            return response()->json(new DatabaseInfoResource([
                'database' => $databaseInfo[0]->database_name,
            ]));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to retrieve database information', 'message' => $e->getMessage()], 500);
        }
    }
}