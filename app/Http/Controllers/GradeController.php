<?php
namespace App\Http\Controllers;

use App\Models\ClassGroup;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GradeController extends Controller
{
    // GET /api/grades
    public function index(Request $request)
    {
        $teacherId = Auth::id();

        // 1) Tanár osztályai
        $classes = ClassGroup::where('teacher_id', $teacherId)->get();

        // 2) Ha class_id param érkezik, betöltjük a diákokat jegyekkel
        $students = [];
        if ($request->filled('class_id')) {
            $class = ClassGroup::where('teacher_id', $teacherId)
                ->findOrFail($request->query('class_id'));

            $students = $class->students()
                ->with(['grades.subject'])
                ->get()
                ->map(function($student) {
                    $grouped = [];
                    foreach ($student->grades as $grade) {
                        $sub = $grade->subject->name;
                        $grouped[$sub][] = [
                            'id'      => $grade->id,
                            'grade'   => $grade->grade,
                            'dated'   => Carbon::parse($grade->graded_at)->format('Y-m-d'),
                            'remarks' => $grade->remarks,
                        ];
                    }
                    return [
                        'id'     => $student->id,
                        'name'   => $student->name,
                        'grades' => $grouped,
                    ];
                });
        }

        return response()->json([
            'classes'  => $classes,
            'students' => $students,
            'subjects' => Subject::all(['id','name']),
        ]);
    }

    // POST /api/grades
    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'grade'      => 'required|numeric|between:1,5',
            'remarks'    => 'nullable|string',
        ]);

        // átnevezzük user_id-re
        $data['user_id']   = $data['student_id'];
        unset($data['student_id']);

        // a többi mező
        $data['teacher_id'] = Auth::id();
        $data['graded_at']  = Carbon::today()->format('Y-m-d');

        $grade = Grade::create($data);
        return response()->json($grade, 201);
    }
}