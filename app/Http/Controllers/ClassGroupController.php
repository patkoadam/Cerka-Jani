<?php

namespace App\Http\Controllers;

use App\Models\ClassGroup;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassGroupController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $classGroup = ClassGroup::create([
            'teacher_id' => Auth::id(), // A bejelentkezett tanár lesz az osztály létrehozója
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json($classGroup, 201);
    }

    public function fetchStudents(Request $request, $classGroupId)
    {
        $classGroup = ClassGroup::findOrFail($classGroupId);
        $students = $classGroup->students;
        return response()->json($students);
    }

    public function indexStudents(Request $request)
    {
        $query = $request->get('query', '');
        // Feltételezzük, hogy a diákok role_id-je például 1 (vagy módosítsd az értéket a saját logikád szerint)
        $students = User::where('role_id', 1)
            ->when($query, function ($q, $query) {
                return $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->get();

        return response()->json($students);
    }



    public function addStudent(Request $request, $classGroupId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $classGroup = ClassGroup::findOrFail($classGroupId);
        $classGroup->students()->attach($request->user_id);

        return response()->json(['message' => 'Diák hozzáadva az osztályhoz.']);
    }


    public function delete($classGroupId, $userId)
    {
        $classGroup = ClassGroup::findOrFail($classGroupId);
        $classGroup->students()->detach($userId);

        return response()->json(['message' => 'Diák eltávolítva az osztályból.']);
    }


    public function index()
    {
        // csak a teacher_id mezővel hozzárendelt ClassGroup-okat adjuk vissza
        $groups = ClassGroup::where('teacher_id', Auth::id())
            ->orderBy('name')
            ->get();

        return response()->json($groups);
    }

    public function indexForTeacher()
    {
        $groups = ClassGroup::where('teacher_id', Auth::id())->get();
        return response()->json($groups);
    }
}
