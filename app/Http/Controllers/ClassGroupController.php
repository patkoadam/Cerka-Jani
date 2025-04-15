<?php

namespace App\Http\Controllers;

use App\Models\ClassGroup;
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

    public function storeSchedule(Request $request, $classGroupId)
    {
        $request->validate([
            'subject'    => 'required|string|max:255',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i',
            'room'       => 'nullable|string|max:255',
        ]);

        // Ellenőrizd, hogy az osztálycsoport létezik
        $classGroup = ClassGroup::findOrFail($classGroupId);

        // A tanár id-je a bejelentkezett userből
        $schedule = $classGroup->schedules()->create([
            'teacher_id' => Auth::id(),
            'subject' => $request->subject,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'room' => $request->room,
        ]);

        return response()->json($schedule, 201);
    }

    public function fetchSchedules(Request $request, $classGroupId)
    {
        $classGroup = ClassGroup::findOrFail($classGroupId);
        // Lekérjük az adott osztály órarendjét, például rendezve nap és kezdési idő szerint:
        $schedules = $classGroup->schedules()->orderBy('day_of_week')->orderBy('start_time')->get();
        return response()->json($schedules);
    }
}
