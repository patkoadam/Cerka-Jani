<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $grades = Grade::with(['subject', 'teacher'])
        ->where('user_id', Auth::id())
        ->paginate(10);

                   

        $users = User::all();
        $subjects = Subject::all();
        $teachers = Teacher::all();

        return response()->json([
            'grades'   => $grades,
            'users'    => $users,
            'subjects' => $subjects,
            'teachers' => $teachers,
        ], 200, ['Access-Control-Allow-Origin' => '*'], JSON_UNESCAPED_UNICODE);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
                'subject_id' => 'required|exists:subjects,id',
                'teacher_id' => 'required|exists:teachers,id',
                'grade'      => 'required|numeric|min:1|max:5',
                'graded_at'  => 'required|date',
                'remarks'    => 'nullable|string'
            ]);
    
            Grade::create($validated);
        } catch (ValidationException $th) {
            return response()->json(['success' => false, 'message' => $th->errors()], 201, ['Access-Control-Allow-Origin' => '*'], JSON_UNESCAPED_UNICODE);
        }
        return response()->json(['success' => true, 'message' => 'Event successfully added!'], 200, ['Access-Control-Allow-Origin' => '*'], JSON_UNESCAPED_UNICODE);
    }

    public function update(Request $request, $id)
    {
        $grade = Grade::findOrFail($id);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'grade'      => 'required|numeric|min:1|max:5',
            'graded_at'  => 'required|date',
            'remarks'    => 'nullable|string'
        ]);

        $grade->update($validated);

        return redirect()->route('grades.index')->with('success', 'Jegy módosítva.');
    }

    public function destroy($id)
    {
        $grade = Grade::findOrFail($id);
        $grade->delete();

        return redirect()->back()->with('success', 'Jegy törölve.');
    }
}
