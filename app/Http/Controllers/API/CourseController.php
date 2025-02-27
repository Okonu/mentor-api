<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected $courseRepository;

    public function __construct(CourseRepositoryInterface $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    /**
     * Get all courses.
     */
    public function index()
    {
        $courses = $this->courseRepository->all();

        return response()->json([
            'status' => 'success',
            'data' => $courses,
        ]);
    }

    /**
     * Get a specific course.
     */
    public function show($id)
    {
        $course = $this->courseRepository->findById($id);

        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $course,
        ]);
    }
}
