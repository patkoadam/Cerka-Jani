<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return response()->json($users, 200, ['Access-Control-Allow-Origin' => '*'], JSON_UNESCAPED_UNICODE);
    }
}
