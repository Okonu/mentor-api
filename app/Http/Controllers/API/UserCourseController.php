<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;

class UserCourseController extends Controller
{
    protected $userRepository;
    protected $courseRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        CourseRepositoryInterface $courseRepository
    ) {
        $this->userRepository = $userRepository;
        $this->courseRepository = $courseRepository;
    }

    /**
     * Add a course to a user's studied courses.
     */
    public function addCourse(Request $request, $courseId)
    {
        $user = $request->attributes->get('user');

        $course = $this->courseRepository->findById($courseId);
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course not found',
            ], 404);
        }

        $result = $this->userRepository->addUserCourse($user['id'], $courseId);

        if (!$result) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course already in user\'s studied courses',
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Course added to user\'s studied courses',
        ]);
    }

    /**
     * Get the user's studied courses.
     */
    public function getUserCourses(Request $request)
    {
        $user = $request->attributes->get('user');

        $courseIds = $this->userRepository->getUserCourses($user['id']);

        $courses = collect($courseIds)->map(function ($courseId) {
            return $this->courseRepository->findById($courseId);
        })->filter()->values();

        return response()->json([
            'status' => 'success',
            'data' => $courses,
        ]);
    }
}
