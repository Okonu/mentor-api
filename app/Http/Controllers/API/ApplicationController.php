<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\MentorService;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    protected $mentorService;

    public function __construct(MentorService $mentorService)
    {
        $this->mentorService = $mentorService;
    }

    /**
     * Get all applications.
     */
    public function index(Request $request)
    {
        $applications = $this->mentorService->getAllApplications();

        return response()->json([
            'status' => 'success',
            'data' => $applications,
        ]);
    }

    /**
     * Get user's applications.
     */
    public function getUserApplications(Request $request)
    {
        $userId = $request->header('X-User-ID', 1);

        $applications = $this->mentorService->getUserApplications($userId);

        return response()->json([
            'status' => 'success',
            'data' => $applications,
        ]);
    }

    /**
     * Approve an application.
     */
    public function approve($id)
    {
        $result = $this->mentorService->approveApplication($id);

        if (!$result['success']) {
            return response()->json([
                'status' => 'error',
                'errors' => $result['errors'],
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Application approved successfully',
            'data' => $result['data'],
        ]);
    }

    /**
     * Reject an application.
     */
    public function reject(Request $request, $id)
    {
        $reason = $request->input('reason');

        $result = $this->mentorService->rejectApplication($id, $reason);

        if (!$result['success']) {
            return response()->json([
                'status' => 'error',
                'errors' => $result['errors'],
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Application rejected successfully',
            'data' => $result['data'],
        ]);
    }
}
