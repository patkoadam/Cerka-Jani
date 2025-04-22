<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function index()
    {
        return Inertia::render("data");
    }

    
}
