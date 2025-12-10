<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'course_id', 'enrollment_date', 'status'];
    protected $useTimestamps = false;

    public function enrollUser($data)
    {
        return $this->insert($data);
    }

    public function getUserEnrollments($user_id)
    {
        return $this->select('enrollments.*, courses.title, courses.description')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->where('enrollments.user_id', $user_id)
                    ->where('enrollments.status', 'approved')
                    ->findAll();
    }

    public function getUserPendingEnrollments($user_id)
    {
        return $this->select('enrollments.*, courses.title, courses.description')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->where('enrollments.user_id', $user_id)
                    ->where('enrollments.status', 'pending')
                    ->findAll();
    }

    public function isAlreadyEnrolled($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->first() !== null;
    }

    public function getPendingEnrollmentsByCourse($course_id)
    {
        return $this->select('enrollments.*, users.name, users.email')
                    ->join('users', 'users.id = enrollments.user_id')
                    ->where('enrollments.course_id', $course_id)
                    ->where('enrollments.status', 'pending')
                    ->findAll();
    }

    public function getApprovedEnrollmentsByCourse($course_id)
    {
        return $this->select('enrollments.*, users.name, users.email')
                    ->join('users', 'users.id = enrollments.user_id')
                    ->where('enrollments.course_id', $course_id)
                    ->where('enrollments.status', 'approved')
                    ->findAll();
    }

    public function approveEnrollment($enrollment_id)
    {
        return $this->update($enrollment_id, ['status' => 'approved']);
    }

    public function rejectEnrollment($enrollment_id)
    {
        return $this->update($enrollment_id, ['status' => 'rejected']);
    }
}
