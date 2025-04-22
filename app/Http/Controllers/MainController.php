<?php

namespace App\Http\Controllers;

use App\Models\ClassGroup;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    public function index()
    {
        $user = Auth::user();
    
        // Tanár első osztálycsoportjának ID‑je
        $classGroup = ClassGroup::where('teacher_id', $user->id)->first();
    
        return Inertia::render('Main', [
            'authUser'     => $user,
            'classGroupId' => $classGroup?->id,  // null, ha még nincs létrehozva
        ]);
    }
}
