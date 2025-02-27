<?php

use App\Http\Controllers\API\ApplicationController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\MentorController;
use App\Http\Controllers\API\UserCourseController;
use App\Http\Middleware\MockAuthMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::group([], function () {
    // Authentication
    Route::post('/register', [AuthController::class, 'register']);

    // Courses - Public read access
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);

    // Mentors - Public read access
    Route::get('/mentors', [MentorController::class, 'index']);
    Route::get('/mentors/{id}', [MentorController::class, 'show']);
});

// Protected routes
Route::middleware(MockAuthMiddleware::class)->group(function () {
    // Courses - Create
    Route::post('/courses', [CourseController::class, 'store']);

    // User-Course Association
    Route::post('/user/courses/{courseId}', [UserCourseController::class, 'addCourse']);
    Route::get('/user/courses', [UserCourseController::class, 'getUserCourses']);

    // Mentor Applications
    Route::post('/mentors/apply', [MentorController::class, 'apply']);
    Route::get('/user/applications', [ApplicationController::class, 'getUserApplications']);
    Route::get('/user/mentor-status', [MentorController::class, 'getUserMentorStatus']);

    // Application Management
    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::put('/applications/{id}/approve', [ApplicationController::class, 'approve']);
    Route::put('/applications/{id}/reject', [ApplicationController::class, 'reject']);
});
