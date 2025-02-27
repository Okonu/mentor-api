<?php

namespace App\Repositories\Mock;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository extends MockRepository implements UserRepositoryInterface
{
    protected function loadInitialData()
    {
        $users = [
            [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(30),
            ],
            [
                'id' => 2,
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now()->subDays(25),
                'updated_at' => now()->subDays(25),
            ],
            [
                'id' => 3,
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now()->subDays(60),
                'updated_at' => now()->subDays(60),
            ],
        ];

        foreach ($users as $user) {
            $this->data->put($user['id'], $user);
        }

        static::$nextId = 4;
    }

    /**
     * Find a user by email.
     */
    public function findByEmail($email)
    {
        return $this->data->firstWhere('email', $email);
    }

    /**
     * Get courses studied by a user.
     */
    public function getUserCourses($userId)
    {
        $userCourses = [
            1 => [1, 3],
            2 => [2, 4],
            3 => [1, 2, 3, 4],
        ];

        return $userCourses[$userId] ?? [];
    }

    /**
     * Check if a user has an active mentor application.
     */
    public function hasActiveApplication($userId)
    {
        return false;
    }

    /**
     * Check if a user is a mentor for any course.
     */
    public function isMentor($userId)
    {
        return false;
    }
}
