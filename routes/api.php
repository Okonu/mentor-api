<?php

use App\Http\Controllers\API\ApplicationController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\MentorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    // Courses
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);

    // Mentors
    Route::get('/mentors', [MentorController::class, 'index']);
    Route::get('/mentors/{id}', [MentorController::class, 'show']);
    Route::post('/mentors/apply', [MentorController::class, 'apply']);

    // Applications
    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::get('/user/applications', [ApplicationController::class, 'getUserApplications']);
    Route::put('/applications/{id}/approve', [ApplicationController::class, 'approve']);
    Route::put('/applications/{id}/reject', [ApplicationController::class, 'reject']);
});
