<?php

namespace App\Repositories\Mock;

use App\Repositories\Interfaces\MentorApplicationRepositoryInterface;

class MentorApplicationRepository extends MockRepository implements MentorApplicationRepositoryInterface
{
    protected function loadInitialData()
    {
        $applications = [
            [
                'id' => 1,
                'user_id' => 1,
                'course_id' => 1,
                'calendly_link' => 'https://calendly.com/johndoe/mentoring',
                'status' => 'approved',
                'rejection_reason' => null,
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(15),
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'course_id' => 2,
                'calendly_link' => 'https://calendly.com/janesmith/mentoring',
                'status' => 'pending',
                'rejection_reason' => null,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'id' => 3,
                'user_id' => 1,
                'course_id' => 3,
                'calendly_link' => 'https://calendly.com/johndoe/webdev-mentoring',
                'status' => 'rejected',
                'rejection_reason' => 'Insufficient experience in this field',
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(25),
            ],
        ];

        foreach ($applications as $application) {
            $this->data->put($application['id'], $application);
        }

        static::$nextId = 4;
    }

    /**
     * Find applications by user and status.
     */
    public function findByUserAndStatus($userId, $status)
    {
        return $this->data->filter(function ($item) use ($userId, $status) {
            return $item['user_id'] == $userId && $item['status'] == $status;
        })->values();
    }

    /**
     * Find pending applications for a user.
     */
    public function findPendingByUser($userId)
    {
        return $this->findByUserAndStatus($userId, 'pending');
    }

    /**
     * Approve an application.
     */
    public function approve($id)
    {
        return $this->update($id, [
            'status' => 'approved',
            'updated_at' => now(),
        ]);
    }

    /**
     * Reject an application.
     */
    public function reject($id, $reason = null)
    {
        return $this->update($id, [
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'updated_at' => now(),
        ]);
    }
}
