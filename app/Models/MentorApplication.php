<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'calendly_link',
        'status',
        'rejection_reason',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the user that submitted this application.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course this application is for.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Check if this application is pending.
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if this application is approved.
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if this application is rejected.
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
