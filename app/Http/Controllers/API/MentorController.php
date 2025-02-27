<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\MentorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MentorController extends Controller
{
    protected $mentorService;

    public function __construct(MentorService $mentorService)
    {
        $this->mentorService = $mentorService;
    }

    /**
     * Get all mentors.
     */
    public function index()
    {
        $mentors = $this->mentorService->getAllMentors();

        return response()->json([
            'status' => 'success',
            'data' => $mentors,
        ]);
    }

    /**
     * Get mentor profile.
     */
    public function show($id)
    {
        $mentorProfile = $this->mentorService->getMentorProfile($id);

        if (!$mentorProfile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mentor not found or user is not a mentor',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $mentorProfile,
        ]);
    }

    /**
     * Apply to become a mentor.
     */
    public function apply(Request $request)
    {
        $userId = $request->header('X-User-ID', 1);

        $result = $this->mentorService->applyToBecomeMentor($userId, $request->all());

        if (!$result['success']) {
            return response()->json([
                'status' => 'error',
                'errors' => $result['errors'],
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Application submitted successfully',
            'data' => $result['data'],
        ], 201);
    }
}
