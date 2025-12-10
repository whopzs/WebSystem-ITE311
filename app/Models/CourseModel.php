<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table      = 'courses';   // Table name
    protected $primaryKey = 'id';        // Primary key

    protected $allowedFields = [
        'title',
        'description',
        'course_number',
        'semester',
        'term',
        'academic_year',
        'instructor_id',
        'day',
        'time',
        'room',
        'created_at',
        'updated_at'
    ];

    // Enable auto timestamps if you want CodeIgniter to handle them
    protected $useTimestamps = true; // This will auto-fill created_at & updated_at
}
