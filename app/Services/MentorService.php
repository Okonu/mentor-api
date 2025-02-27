<?php

namespace App\Services;

use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\MentorApplicationRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Validator;

class MentorService
{
    protected $userRepository;
    protected $courseRepository;
    protected $applicationRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        CourseRepositoryInterface $courseRepository,
        MentorApplicationRepositoryInterface $applicationRepository
    ) {
        $this->userRepository = $userRepository;
        $this->courseRepository = $courseRepository;
        $this->applicationRepository = $applicationRepository;
    }

    /**
     * Get all approved mentors.
     */
    public function getAllMentors()
    {
        // Find all approved mentor applications
        $approvedApplications = $this->applicationRepository->all()->filter(function ($application) {
            return $application['status'] === 'approved';
        });

        // Group by user ID for unique mentors
        $mentorIds = $approvedApplications->pluck('user_id')->unique()->values();

        // Get user details for each mentor
        $mentors = $mentorIds->map(function ($userId) use ($approvedApplications) {
            $user = $this->userRepository->findById($userId);
            $mentorApplications = $approvedApplications->filter(function ($app) use ($userId) {
                return $app['user_id'] === $userId;
            });

            $mentorCourses = $mentorApplications->map(function ($app) {
                $course = $this->courseRepository->findById($app['course_id']);
                return array_merge($course, [
                    'calendly_link' => $app['calendly_link'],
                ]);
            });

            return [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'mentored_courses' => $mentorCourses->values(),
            ];
        });

        return $mentors->values();
    }

    /**
     * Apply to become a mentor.
     */
    public function applyToBecomeMentor($userId, array $data)
    {
        $validator = Validator::make($data, [
            'course_id' => 'required|integer',
            'calendly_link' => 'required|url',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors(),
            ];
        }

        // Check if user exists
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            return [
                'success' => false,
                'errors' => ['user' => 'User not found'],
            ];
        }

        // Check if course exists
        $course = $this->courseRepository->findById($data['course_id']);
        if (!$course) {
            return [
                'success' => false,
                'errors' => ['course_id' => 'Course not found'],
            ];
        }

        // Check if user has any pending applications
        $pendingApplications = $this->applicationRepository->findPendingByUser($userId);
        if ($pendingApplications->isNotEmpty()) {
            return [
                'success' => false,
                'errors' => ['application' => 'You already have a pending mentor application'],
            ];
        }

        // Create the application
        $application = $this->applicationRepository->create([
            'user_id' => $userId,
            'course_id' => $data['course_id'],
            'calendly_link' => $data['calendly_link'],
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'success' => true,
            'data' => $application,
        ];
    }

    /**
     * Approve a mentor application.
     */
    public function approveApplication($applicationId)
    {
        $application = $this->applicationRepository->findById($applicationId);

        if (!$application) {
            return [
                'success' => false,
                'errors' => ['application' => 'Application not found'],
            ];
        }

        if ($application['status'] !== 'pending') {
            return [
                'success' => false,
                'errors' => ['application' => 'Only pending applications can be approved'],
            ];
        }

        $updatedApplication = $this->applicationRepository->approve($applicationId);

        return [
            'success' => true,
            'data' => $updatedApplication,
        ];
    }

    /**
     * Reject a mentor application.
     */
    public function rejectApplication($applicationId, $reason = null)
    {
        $application = $this->applicationRepository->findById($applicationId);

        if (!$application) {
            return [
                'success' => false,
                'errors' => ['application' => 'Application not found'],
            ];
        }

        if ($application['status'] !== 'pending') {
            return [
                'success' => false,
                'errors' => ['application' => 'Only pending applications can be rejected'],
            ];
        }

        $updatedApplication = $this->applicationRepository->reject($applicationId, $reason);

        return [
            'success' => true,
            'data' => $updatedApplication,
        ];
    }

    /**
     * Get all applications.
     */
    public function getAllApplications()
    {
        $applications = $this->applicationRepository->all();

        // Enrich application data with user and course info
        return $applications->map(function ($application) {
            $user = $this->userRepository->findById($application['user_id']);
            $course = $this->courseRepository->findById($application['course_id']);

            return array_merge($application, [
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                ],
                'course' => [
                    'id' => $course['id'],
                    'name' => $course['name'],
                    'description' => $course['description'],
                ],
            ]);
        })->values();
    }

    /**
     * Get user's applications.
     */
    public function getUserApplications($userId)
    {
        $applications = $this->applicationRepository->all()->filter(function ($application) use ($userId) {
            return $application['user_id'] == $userId;
        });

        // Enrich application data with course info
        return $applications->map(function ($application) {
            $course = $this->courseRepository->findById($application['course_id']);

            return array_merge($application, [
                'course' => [
                    'id' => $course['id'],
                    'name' => $course['name'],
                    'description' => $course['description'],
                ],
            ]);
        })->values();
    }

    /**
     * Get mentor profile.
     */
    public function getMentorProfile($userId)
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            return null;
        }

        $approvedApplications = $this->applicationRepository->findByUserAndStatus($userId, 'approved');

        if ($approvedApplications->isEmpty()) {
            return null;
        }

        $mentorCourses = $approvedApplications->map(function ($app) {
            $course = $this->courseRepository->findById($app['course_id']);
            return array_merge($course, [
                'calendly_link' => $app['calendly_link'],
            ]);
        });

        return [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'mentored_courses' => $mentorCourses->values(),
        ];
    }
}
