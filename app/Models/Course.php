<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the users who studied this course.
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'user_courses');
    }

    /**
     * Get the approved mentors for this course.
     */
    public function mentors()
    {
        return $this->belongsToMany(User::class, 'mentor_applications')
            ->wherePivot('status', 'approved');
    }

    /**
     * Get all mentor applications for this course.
     */
    public function mentorApplications()
    {
        return $this->hasMany(MentorApplication::class);
    }
}
