<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    // GET /class-groups/{classGroup}/schedules?start=YYYY-MM-DD&end=YYYY-MM-DD
    public function index($classGroup, Request $request)
    {
        $data = $request->validate([
            'start' => 'required|date',
            'end'   => 'required|date',
        ]);

        $list = Schedule::with('subject')
            ->where('class_group_id', $classGroup)
            ->where('teacher_id', Auth::id())
            ->whereBetween('day', [$data['start'], $data['end']])
            ->orderBy('day')
            ->orderBy('time')
            ->get();

        return response()->json($list);
    }

    // POST /class-groups/{classGroup}/schedules
    public function store($classGroup, Request $request)
    {
        $data = $request->validate([
            'day'        => 'required|date',
            'time'       => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $data['class_group_id'] = $classGroup;
        $data['teacher_id']     = Auth::id();

        $sched = Schedule::create($data);

        return response()->json($sched, 201);
    }
}
