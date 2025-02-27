<?php

namespace App\Repositories\Mock;

use App\Repositories\Interfaces\CourseRepositoryInterface;

class CourseRepository extends MockRepository implements CourseRepositoryInterface
{
    protected function loadInitialData()
    {
        $courses = [
            [
                'id' => 1,
                'name' => 'Computer Science 101',
                'description' => 'Introduction to Computer Science',
                'created_at' => now()->subDays(100),
                'updated_at' => now()->subDays(100),
            ],
            [
                'id' => 2,
                'name' => 'Data Structures',
                'description' => 'Advanced data structures and algorithms',
                'created_at' => now()->subDays(95),
                'updated_at' => now()->subDays(95),
            ],
            [
                'id' => 3,
                'name' => 'Web Development',
                'description' => 'Full-stack web development with modern frameworks',
                'created_at' => now()->subDays(90),
                'updated_at' => now()->subDays(90),
            ],
            [
                'id' => 4,
                'name' => 'Machine Learning',
                'description' => 'Introduction to machine learning algorithms',
                'created_at' => now()->subDays(85),
                'updated_at' => now()->subDays(85),
            ],
        ];

        foreach ($courses as $course) {
            $this->data->put($course['id'], $course);
        }

        static::$nextId = 5;
    }

    /**
     * Get all mentors for a course.
     */
    public function getCourseMentors($courseId)
    {
        return [];
    }
}
