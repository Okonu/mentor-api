<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the courses this user studied.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'user_courses');
    }

    /**
     * Get the mentor applications submitted by this user.
     */
    public function mentorApplications()
    {
        return $this->hasMany(MentorApplication::class);
    }

    /**
     * Check if the user has any active mentor applications.
     */
    public function hasActiveApplication()
    {
        return $this->mentorApplications()
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Check if the user is an approved mentor for any course.
     */
    public function isMentor()
    {
        return $this->mentorApplications()
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Get the courses this user is approved to mentor.
     */
    public function mentoredCourses()
    {
        return $this->belongsToMany(Course::class, 'mentor_applications')
            ->wherePivot('status', 'approved');
    }
}
