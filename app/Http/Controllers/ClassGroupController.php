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

    public function storeSchedule(Request $request, $classGroupId)
    {
        $request->validate([
            'day'        => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'time'       => 'required|date_format:H:i',
            'endTime'    => 'required|date_format:H:i',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $user = $request->user();

        // Csak a tanár menthet a saját osztályához
        $classGroup = ClassGroup::where('id', $classGroupId)
            ->where('teacher_id', $user->id)
            ->firstOrFail();

        $schedule = Schedule::create([
            'class_group_id' => $classGroupId,
            'teacher_id'     => $user->id,
            'day'            => $request->day,
            'start_time'     => $request->time,
            'end_time'       => $request->endTime,
            'subject_id'     => $request->subject_id,
        ]);

        return response()->json($schedule, 201);
    }

    public function fetchSchedules(Request $request, $classGroupId)
    {
        $request->validate([
            'start' => 'required|date',
            'end'   => 'required|date',
        ]);

        $user  = $request->user();
        $start = $request->start;
        $end   = $request->end;

        // Ha a migrációd a dátumot 'date' mezőben tárolja, ezt használjuk:
        $query = Schedule::with('subject')
            ->where('class_group_id', $classGroupId);

        if ($user->role->name === 'Tanár' || strtolower($user->role->name) === 'teacher') {
            $query->where('teacher_id', $user->id);
        }

        $schedules = $query
            ->whereBetween('date', [$start, $end])   // <— itt 'date', nem 'day'
            ->get()
            ->map(function ($s) {
                return [
                    'id'        => $s->id,
                    'date'      => $s->date,            // ISO dátum
                    'day'       => $s->date,            // vagy itt is date, majd a frontenden fordítod névre
                    'time'      => $s->start_time,
                    'endTime'   => $s->end_time,
                    'subject'   => [
                        'id'   => $s->subject->id,
                        'name' => $s->subject->name,
                    ],
                ];
            });

        return response()->json($schedules);
    }

    public function index()
    {
        // csak a teacher_id mezővel hozzárendelt ClassGroup-okat adjuk vissza
        $groups = ClassGroup::where('teacher_id', Auth::id())
            ->orderBy('name')
            ->get();

        return response()->json($groups);
    }
}
