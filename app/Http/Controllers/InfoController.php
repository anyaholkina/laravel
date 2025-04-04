<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ServerInfoResource;
use App\Http\Resources\ClientInfoResource;
use App\Http\Resources\DatabaseInfoResource;

class InfoController extends Controller
{
    public function server()
    {
        return response()->json(new ServerInfoResource(phpinfo()));
    }

    public function client(Request $request)
    {
        return response()->json(new ClientInfoResource([
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]));
    }

    public function database()
    {
        $database = env('DB_DATABASE');
        return response()->json(new DatabaseInfoResource([
            'database' => $database,
        ]));
    }
}
