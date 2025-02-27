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
        $approvedApplications = $this->applicationRepository->all()->filter(function ($application) {
            return $application['status'] === 'approved';
        });

        $mentorIds = $approvedApplications->pluck('user_id')->unique()->values();

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

        $user = $this->userRepository->findById($userId);
        if (!$user) {
            return [
                'success' => false,
                'errors' => ['user' => 'User not found'],
            ];
        }

        $course = $this->courseRepository->findById($data['course_id']);
        if (!$course) {
            return [
                'success' => false,
                'errors' => ['course_id' => 'Course not found'],
            ];
        }

        $userCourses = $this->userRepository->getUserCourses($userId);
        if (!in_array($data['course_id'], $userCourses)) {
            return [
                'success' => false,
                'errors' => ['course_id' => 'You can only apply to mentor courses you have studied'],
            ];
        }

        $pendingApplications = $this->applicationRepository->findPendingByUser($userId);
        if ($pendingApplications->isNotEmpty()) {
            return [
                'success' => false,
                'errors' => ['application' => 'You already have a pending mentor application'],
            ];
        }

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

    /**
     * Check if a user has any active mentor applications.
     */
    public function hasActiveApplication($userId)
    {
        $pendingApplications = $this->applicationRepository->findPendingByUser($userId);
        return $pendingApplications->isNotEmpty();
    }

    /**
     * Check if a user is an approved mentor for any course.
     */
    public function isMentor($userId)
    {
        $approvedApplications = $this->applicationRepository->findByUserAndStatus($userId, 'approved');
        return $approvedApplications->isNotEmpty();
    }

    /**
     * Get user's mentor status for all courses.
     */
    public function getUserMentorStatus($userId)
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            return null;
        }

        $studiedCourseIds = $this->userRepository->getUserCourses($userId);
        $studiedCourses = collect($studiedCourseIds)->map(function ($courseId) {
            return $this->courseRepository->findById($courseId);
        })->filter()->values();

        $applications = $this->applicationRepository->all()->filter(function ($app) use ($userId) {
            return $app['user_id'] == $userId;
        });

        $courseApplications = [];
        foreach ($applications as $application) {
            $courseId = $application['course_id'];
            $courseApplications[$courseId] = $application;
        }

        $coursesWithStatus = $studiedCourses->map(function ($course) use ($courseApplications) {
            $status = 'not_applied';
            $application = $courseApplications[$course['id']] ?? null;

            if ($application) {
                $status = $application['status'];
            }

            return array_merge($course, [
                'mentor_status' => $status,
                'application' => $application,
            ]);
        });

        return [
            'user' => $user,
            'courses' => $coursesWithStatus,
        ];
    }
}
