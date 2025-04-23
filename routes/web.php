<?php

use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\NaptarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeacherScheduleController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\JegyekController;
use App\Http\Controllers\HianyzasokController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ClassGroupController;
use App\Http\Controllers\KezdolapController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentScheduleController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UzenetController;
use Inertia\Inertia;



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/naptar', [NaptarController::class, 'index'])->name('naptar');
    Route::get('/api/events', [EventController::class, 'index']);
    Route::post('/api/events', [EventController::class, 'store'])->name('events');
    Route::get('/main', [MainController::class, 'index'])->name('main');
    Route::get('/jegyek/{id}', [JegyekController::class, 'index'])->name('jegyek');
    Route::get('/hianyzasok/{id}', [HianyzasokController::class, 'index'])->name('hianyzasok');
    Route::get('/data/{id}', [DataController::class, 'index'])->name('data'); //profile oldal
    Route::get('/kezdolap', [KezdolapController::class, 'index'])->name('kezdolap'); //kezdolap oldal
    Route::get('/uzenetek', [UzenetController::class, 'index'])->name('uzenetek'); //uzenetek oldal
    Route::get('/grades', [GradeController::class, 'index'])->name('grade');
    Route::get('/roles', [RoleController::class, 'index'])->name('role');

    Route::post('/api/class-groups', [ClassGroupController::class, 'store'])->name('class-groups.store');
    Route::get('/api/class-groups/{classGroupId}/students', [ClassGroupController::class, 'fetchStudents'])->name('class-groups.students.index');
    Route::post('/api/class-groups/{classGroupId}/students', [ClassGroupController::class, 'addStudent'])->name('class-groups.students.add');
    Route::delete('/api/class-groups/{classGroupId}/students/{userId}', [ClassGroupController::class, 'delete'])->name('class-groups.students.delete');
    Route::get('/api/students', [ClassGroupController::class, 'indexStudents'])->name('students.index');




    Route::get('/subjects', [SubjectController::class, 'index'])
        ->name('subjects.index');
    Route::get('/class-groups/{classGroup}/schedules', [ScheduleController::class, 'index'])
        ->name('schedules.index');
    Route::post('/class-groups/{classGroup}/schedules', [ScheduleController::class, 'store'])
        ->name('schedules.store');
    Route::get('/class-groups', [ClassGroupController::class, 'index']);
    Route::get('/class-groups', [ClassGroupController::class, 'indexForTeacher']);




    Route::get('/self/schedule', [ScheduleController::class, 'selfSchedule']);

});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');


require __DIR__ . '/auth.php';
