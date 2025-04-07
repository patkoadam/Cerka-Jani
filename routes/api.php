<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NaptarController;
use App\Http\Controllers\EventController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth');

Route::middleware('auth')->group(function () {
    
});

