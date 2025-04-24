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
use App\Http\Controllers\UserController;
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

    Route::get('/class-groups',                          [ClassGroupController::class, 'index']);
    Route::post('/class-groups',                          [ClassGroupController::class, 'store']);

    // Osztályhoz tartozó diákok kezelése
    Route::get('/class-groups/{classGroup}/students',    [ClassGroupController::class, 'students']);
    Route::post('/class-groups/{classGroup}/students',    [ClassGroupController::class, 'addStudent']);
    Route::delete('/class-groups/{classGroup}/students/{user}', [ClassGroupController::class, 'removeStudent']);

    // Diákok keresése név vagy email alapján
    Route::get('/students',                              [UserController::class, 'index']);




    Route::get('/subjects', [SubjectController::class, 'index'])
        ->name('subjects.index');
    Route::get('/class-groups/{classGroup}/schedules', [ScheduleController::class, 'index'])
        ->name('schedules.index');
    Route::post('/class-groups/{classGroup}/schedules', [ScheduleController::class, 'store'])
        ->name('schedules.store');
    Route::get('/class-groups', [ClassGroupController::class, 'index']);
    Route::get('/class-groups', [ClassGroupController::class, 'indexForTeacher']);




    Route::get('/self/schedule', [ScheduleController::class, 'selfSchedule']);




    Route::get('/grades', [GradeController::class, 'index']);
    Route::post('/grades', [GradeController::class, 'store']);
    Route::get('/self/grades', [GradeController::class, 'selfIndex']);


    Route::get('/self/classmates', [ClassGroupController::class, 'selfStudents']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');


require __DIR__ . '/auth.php';
