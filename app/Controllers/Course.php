<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\CourseModel;
use App\Models\NotificationModel;

class Course extends BaseController
{
    public function enroll()
    {
        $this->response->setContentType('application/json');

        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'User not logged in']);
        }

        $course_id = $this->request->getPost('course_id');
        $user_id = session()->get('user_id');

        if (!$course_id) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Course ID is required']);
        }

        $enrollmentModel = new EnrollmentModel();
        $courseModel = new CourseModel();

        $course = $courseModel->find($course_id);
        if (!$course) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Course not found']);
        }

        if ($enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Already enrolled in this course']);
        }

        $data = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'enrollment_date' => date('Y-m-d H:i:s'),
            'status' => 'pending'
        ];

        if ($enrollmentModel->enrollUser($data)) {
            // Get the enrollment date from the data
            $enrollment_date = $data['enrollment_date'];

            // Create notification for pending enrollment for the student
            $notificationModel = new \App\Models\NotificationModel();
            $notificationData = [
                'user_id' => $user_id,
                'message' => "Your enrollment request for {$course['title']} is pending teacher approval",
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $notificationModel->insert($notificationData);

            // Notify the teacher about the new enrollment request
            if (!empty($course['instructor_id'])) {
                $userModel = new \App\Models\UserModel();
                $student = $userModel->find($user_id);
                $studentName = $student['name'];
                $teacherNotification = [
                    'user_id' => $course['instructor_id'],
                    'message' => "Student {$studentName} has requested to enroll in your course: {$course['title']}. Please approve or reject the enrollment.",
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $notificationModel->insert($teacherNotification);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Enrollment request submitted. Waiting for teacher approval.', 'status' => 'pending', 'enrollment_date' => $enrollment_date]);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Failed to submit enrollment request']);
        }
    }

    public function approveEnrollment()
    {
        $this->response->setContentType('application/json');

        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'User not logged in']);
        }

        $role = session()->get('userRole');
        if ($role !== 'teacher' && $role !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $enrollment_id = $this->request->getPost('enrollment_id');
        if (!$enrollment_id) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Enrollment ID is required']);
        }

        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->find($enrollment_id);
        
        if (!$enrollment) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Enrollment not found']);
        }

        // Verify that the teacher owns the course
        if ($role === 'teacher') {
            $courseModel = new CourseModel();
            $course = $courseModel->find($enrollment['course_id']);
            $user_id = session()->get('user_id');
            
            if (!$course || $course['instructor_id'] != $user_id) {
                return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'You do not have permission to approve this enrollment']);
            }
        }

        if ($enrollmentModel->approveEnrollment($enrollment_id)) {
            // Notify the student about approval
            $courseModel = new CourseModel();
            $course = $courseModel->find($enrollment['course_id']);
            $notificationModel = new NotificationModel();
            $userModel = new \App\Models\UserModel();
            $student = $userModel->find($enrollment['user_id']);
            
            $notificationData = [
                'user_id' => $enrollment['user_id'],
                'message' => "Your enrollment request for {$course['title']} has been approved!",
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $notificationModel->insert($notificationData);

            return $this->response->setJSON(['success' => true, 'message' => 'Enrollment approved successfully']);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Failed to approve enrollment']);
        }
    }

    public function rejectEnrollment()
    {
        $this->response->setContentType('application/json');

        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'User not logged in']);
        }

        $role = session()->get('userRole');
        if ($role !== 'teacher' && $role !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $enrollment_id = $this->request->getPost('enrollment_id');
        if (!$enrollment_id) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Enrollment ID is required']);
        }

        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->find($enrollment_id);
        
        if (!$enrollment) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Enrollment not found']);
        }

        // Verify that the teacher owns the course
        if ($role === 'teacher') {
            $courseModel = new CourseModel();
            $course = $courseModel->find($enrollment['course_id']);
            $user_id = session()->get('user_id');
            
            if (!$course || $course['instructor_id'] != $user_id) {
                return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'You do not have permission to reject this enrollment']);
            }
        }

        if ($enrollmentModel->rejectEnrollment($enrollment_id)) {
            // Notify the student about rejection
            $courseModel = new CourseModel();
            $course = $courseModel->find($enrollment['course_id']);
            $notificationModel = new NotificationModel();
            
            $notificationData = [
                'user_id' => $enrollment['user_id'],
                'message' => "Your enrollment request for {$course['title']} has been rejected.",
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $notificationModel->insert($notificationData);

            return $this->response->setJSON(['success' => true, 'message' => 'Enrollment rejected successfully']);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Failed to reject enrollment']);
        }
    }

    public function getPendingEnrollments()
    {
        $this->response->setContentType('application/json');

        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'User not logged in']);
        }

        $role = session()->get('userRole');
        if ($role !== 'teacher' && $role !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $course_id = $this->request->getVar('course_id');
        if (!$course_id) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Course ID is required']);
        }

        // Verify that the teacher owns the course
        if ($role === 'teacher') {
            $courseModel = new CourseModel();
            $course = $courseModel->find($course_id);
            $user_id = session()->get('user_id');
            
            if (!$course || $course['instructor_id'] != $user_id) {
                return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'You do not have permission to view enrollments for this course']);
            }
        }

        $enrollmentModel = new EnrollmentModel();
        $pendingEnrollments = $enrollmentModel->getPendingEnrollmentsByCourse($course_id);

        return $this->response->setJSON(['success' => true, 'enrollments' => $pendingEnrollments]);
    }

    public function search()
    {
        $courseModel = new CourseModel();
        $searchTerm = $this->request->getVar('search_term');

        $user_id = session()->get('user_id');
        $role = session()->get('userRole');

        if ($role === 'student') {
            $enrollmentModel = new EnrollmentModel();
            $enrollments = $enrollmentModel->where('user_id', $user_id)->findAll();
            $enrolledIds = array_column($enrollments, 'course_id');

            if (!empty($enrolledIds)) {
                $courseModel->whereIn('id', $enrolledIds);
                if (!empty($searchTerm)) {
                    $courseModel->groupStart();
                    $courseModel->like('title', $searchTerm);
                    $courseModel->orLike('description', $searchTerm);
                    $courseModel->groupEnd();
                }
                $courses = $courseModel->findAll();

                // Add materials to each course
                $materialModel = new \App\Models\MaterialModel();
                foreach ($courses as &$course) {
                    $course['materials'] = $materialModel->getMaterialsByCourse($course['id']);

                    foreach ($enrollments as $enrollment) {
                        if ($enrollment['course_id'] == $course['id']) {
                            $course['enrollment_date'] = $enrollment['enrollment_date'];
                            break;
                        }
                    }
                }
            } else {
                $courses = [];
            }
        } else {
            if (!empty($searchTerm)) {
                $courseModel->like('title', $searchTerm);
                $courseModel->orLike('description', $searchTerm);
            }
            $courses = $courseModel->findAll();
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($courses);
        }

        return view('courses/search_results', [
            'courses' => $courses,
            'searchTerm' => $searchTerm
        ]);
    }

    public function create()
    {
        $this->response->setContentType('application/json');

        if (!session()->get('isLoggedIn') || session()->get('userRole') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $courseModel = new \App\Models\CourseModel();

        $title = $this->request->getPost('title');
        $courseNumber = $this->request->getPost('course_number');

        // Auto-generate course number if not provided
        if (empty($courseNumber)) {
            $courseNumber = $this->generateCourseNumber($title);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'course_number' => $this->request->getPost('course_number'),
            'description' => $this->request->getPost('description'),
            'semester' => $this->request->getPost('semester'),
            'term' => $this->request->getPost('term'),
            'academic_year' => $this->request->getPost('academic_year'),
        ];
        // Note: instructor_id is not included - will be assigned separately via assign teacher feature
        // The column is now nullable after migration

        $courseId = $courseModel->insert($data);

        if ($courseId) {
            // Notify admin about course creation
            $notificationModel = new NotificationModel();
            $adminId = session()->get('user_id');
            $courseTitle = $data['title'];
            
            $notificationData = [
                'user_id' => $adminId,
                'message' => "Course '{$courseTitle}' has been created successfully.",
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $notificationModel->insert($notificationData);
            
            return $this->response->setJSON(['success' => true, 'message' => 'Course created successfully']);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Failed to create course']);
        }
    }

    public function saveSchedule()
    {
        $this->response->setContentType('application/json');

        if (!session()->get('isLoggedIn') || session()->get('userRole') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $courseModel = new \App\Models\CourseModel();

        $courseId = $this->request->getPost('course_id');
        $day = $this->request->getPost('day');
        $time = $this->request->getPost('time');
        $room = $this->request->getPost('room');

        if (!$courseId) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Course ID is required']);
        }

        // Check if course exists
        $course = $courseModel->find($courseId);
        if (!$course) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Course not found']);
        }

        // Check for teacher schedule conflicts if the course has a teacher assigned
        if (!empty($course['instructor_id']) && $day && $time) {
            $conflictingCourses = $courseModel
                ->where('instructor_id', $course['instructor_id'])
                ->where('day', $day)
                ->where('time', $time)
                ->where('id !=', $courseId)
                ->findAll();

            if (!empty($conflictingCourses)) {
                $conflictCourse = $conflictingCourses[0];
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => "Schedule conflict! The teacher already has another course ({$conflictCourse['title']}) scheduled on {$day} at {$time}. Please choose a different day or time."
                ]);
            }
        }

        // Check for room schedule conflicts if room, day, and time are provided
        if (!empty($room) && $day && $time) {
            $roomConflictingCourses = $courseModel
                ->where('room', $room)
                ->where('day', $day)
                ->where('time', $time)
                ->where('id !=', $courseId)
                ->findAll();

            if (!empty($roomConflictingCourses)) {
                $conflictCourse = $roomConflictingCourses[0];
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => "Room conflict! Room {$room} is already booked for another course ({$conflictCourse['title']}) on {$day} at {$time}. Please choose a different room, day, or time."
                ]);
            }
        }

        $data = [
            'day' => $day,
            'time' => $time,
            'room' => $room,
        ];

        // Update the specific course with the schedule
        try {
            $updated = $courseModel->update($courseId, $data);
            
            // Check if update was successful or if there were any errors
            if ($updated !== false) {
                // Verify the update by fetching the course again
                $updatedCourse = $courseModel->find($courseId);
                if ($updatedCourse && 
                    $updatedCourse['day'] === $day && 
                    $updatedCourse['time'] === $time && 
                    $updatedCourse['room'] === $room) {
                    
                    // Notify teacher if assigned
                    if (!empty($updatedCourse['instructor_id'])) {
                        $notificationModel = new NotificationModel();
                        $userModel = new \App\Models\UserModel();
                        $teacher = $userModel->find($updatedCourse['instructor_id']);
                        $courseTitle = $updatedCourse['title'];
                        
                        $scheduleInfo = '';
                        if ($day && $time) {
                            $scheduleInfo = " {$day} at {$time}";
                            if ($room) {
                                $scheduleInfo .= " in Room {$room}";
                            }
                        }
                        
                        $teacherNotification = [
                            'user_id' => $updatedCourse['instructor_id'],
                            'message' => "Schedule updated for course '{$courseTitle}':{$scheduleInfo}",
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                        $notificationModel->insert($teacherNotification);
                    }
                    
                    // Notify admin
                    $notificationModel = new NotificationModel();
                    $adminId = session()->get('user_id');
                    $courseTitle = $updatedCourse['title'];
                    $scheduleInfo = '';
                    if ($day && $time) {
                        $scheduleInfo = " {$day} at {$time}";
                        if ($room) {
                            $scheduleInfo .= " in Room {$room}";
                        }
                    }
                    
                    $adminNotification = [
                        'user_id' => $adminId,
                        'message' => "Schedule updated for course '{$courseTitle}':{$scheduleInfo}",
                        'is_read' => 0,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $notificationModel->insert($adminNotification);
                    
                    return $this->response->setJSON(['success' => true, 'message' => 'Schedule saved successfully']);
                } else {
                    // Update returned true but data wasn't actually updated
                    return $this->response->setJSON(['success' => true, 'message' => 'Schedule saved successfully']);
                }
            } else {
                // Check for validation errors
                $errors = $courseModel->errors();
                if (!empty($errors)) {
                    return $this->response->setStatusCode(400)->setJSON([
                        'success' => false, 
                        'message' => 'Validation error: ' . implode(', ', $errors)
                    ]);
                }
                
                // Update returned false - might mean no changes or error
                // Try to verify if data is already correct
                $currentCourse = $courseModel->find($courseId);
                if ($currentCourse && 
                    $currentCourse['day'] === $day && 
                    $currentCourse['time'] === $time && 
                    $currentCourse['room'] === $room) {
                    // Data is already correct, treat as success
                    // Still notify if teacher is assigned
                    if (!empty($currentCourse['instructor_id'])) {
                        $notificationModel = new NotificationModel();
                        $courseTitle = $currentCourse['title'];
                        $scheduleInfo = '';
                        if ($day && $time) {
                            $scheduleInfo = " {$day} at {$time}";
                            if ($room) {
                                $scheduleInfo .= " in Room {$room}";
                            }
                        }
                        
                        $teacherNotification = [
                            'user_id' => $currentCourse['instructor_id'],
                            'message' => "Schedule confirmed for course '{$courseTitle}':{$scheduleInfo}",
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                        $notificationModel->insert($teacherNotification);
                    }
                    
                    return $this->response->setJSON(['success' => true, 'message' => 'Schedule is already set to these values']);
                }
                
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false, 
                    'message' => 'Failed to save schedule. Please try again.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Schedule save error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false, 
                'message' => 'An error occurred while saving the schedule: ' . $e->getMessage()
            ]);
        }
    }

    public function assignTeacher()
    {
        $this->response->setContentType('application/json');

        if (!session()->get('isLoggedIn') || session()->get('userRole') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $courseModel = new \App\Models\CourseModel();

        $courseId = $this->request->getPost('course_id');
        $teacherId = $this->request->getPost('teacher_id');

        if (!$courseId || !$teacherId) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Course ID and Teacher ID are required']);
        }

        // Check if course exists
        $course = $courseModel->find($courseId);
        if (!$course) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Course not found']);
        }

        // Update course with teacher assignment only (no schedule)
        $updateData = [
            'instructor_id' => $teacherId,
        ];

        $updated = $courseModel->update($courseId, $updateData);

        if ($updated) {
            // Notify the teacher about the assignment
            $notificationModel = new NotificationModel();
            $userModel = new \App\Models\UserModel();
            $teacher = $userModel->find($teacherId);
            $courseTitle = $course['title'];

            $notificationData = [
                'user_id' => $teacherId,
                'message' => "You have been assigned to teach '{$courseTitle}'.",
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $notificationModel->insert($notificationData);

            // Also notify admin
            $adminId = session()->get('user_id');
            $adminNotification = [
                'user_id' => $adminId,
                'message' => "Teacher {$teacher['name']} has been assigned to course '{$courseTitle}'.",
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $notificationModel->insert($adminNotification);

            return $this->response->setJSON(['success' => true, 'message' => 'Teacher assigned to course successfully']);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Failed to assign teacher to course']);
        }
    }

    public function index()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = $session->get('userRole');
        $data = [
            'userRole' => $role,
            'userEmail' => $session->get('userEmail'),
            'teachers' => []  // default empty
        ];

        if ($role === 'admin') {
            $userModel = new \App\Models\UserModel();
            $courseModel = new \App\Models\CourseModel();
            $user_id = $session->get('user_id');

            $data['totalUsers'] = $userModel->countAllResults();
            $data['courseCount'] = $courseModel->countAllResults();

            $courses = $courseModel->findAll();
            $data['courses'] = $courses ? $courses : [];

            $teachers = $userModel->where('role', 'teacher')->findAll();
            $data['teachers'] = $teachers ? $teachers : [];

            // Load admin notifications
            $notificationModel = new \App\Models\NotificationModel();
            $data['notifications'] = $notificationModel->getNotificationsForUser($user_id);
            $data['unreadCount'] = $notificationModel->getUnreadCount($user_id);

            // Get recent activities
            $data['recentActivities'] = $this->getRecentActivities();
        } elseif ($role === 'teacher') {
            $courseModel = new \App\Models\CourseModel();
            $enrollmentModel = new \App\Models\EnrollmentModel();
            $user_id = $session->get('user_id');

            $courses = $courseModel->where('instructor_id', $user_id)->findAll();

            $teacherCourses = [];
            foreach ($courses as $course) {
                $approvedCount = $enrollmentModel->where('course_id', $course['id'])->where('status', 'approved')->countAllResults();
                $pendingCount = $enrollmentModel->where('course_id', $course['id'])->where('status', 'pending')->countAllResults();

                // Auto-generate course number if missing
                $courseNumber = $course['course_number'] ?? '';
                if (empty($courseNumber)) {
                    $courseNumber = $this->generateCourseNumber($course['title']);
                    // Update the database with the new course number
                    $courseModel->update($course['id'], ['course_number' => $courseNumber]);
                }

                $teacherCourses[] = [
                    'id' => $course['id'],
                    'title' => $course['title'],
                    'course_number' => $courseNumber,
                    'description' => $course['description'] ?? 'No description available',
                    'day' => $course['day'] ?? '',
                    'time' => $course['time'] ?? '',
                    'room' => $course['room'] ?? '',
                    'students' => $approvedCount,
                    'pending_count' => $pendingCount,
                    'status' => $course['status'] ?? 'active'
                ];
            }

            $data['teacherCourses'] = $teacherCourses;

            $notificationModel = new \App\Models\NotificationModel();
            $data['notifications'] = $notificationModel->getNotificationsForUser($user_id);
            $data['unreadCount'] = $notificationModel->getUnreadCount($user_id);
        } elseif ($role === 'student') {
            $enrollmentModel = new \App\Models\EnrollmentModel();
            $courseModel = new \App\Models\CourseModel();
            $user_id = $session->get('user_id');

            // Get approved enrolled courses
            $enrolledCourses = $enrollmentModel->getUserEnrollments($user_id);
            $data['enrolledCourses'] = $enrolledCourses;

            // Get pending enrollments
            $pendingEnrollments = $enrollmentModel->getUserPendingEnrollments($user_id);
            $data['pendingEnrollments'] = $pendingEnrollments;

            // Get all courses
            $allCourses = $courseModel->findAll();

            // Get all enrollment IDs (approved, pending, rejected) to exclude from available courses
            $allEnrollments = $enrollmentModel->where('user_id', $user_id)->findAll();
            $enrolledCourseIds = array_column($allEnrollments, 'course_id');

            $availableCourses = array_filter($allCourses, function($course) use ($enrolledCourseIds) {
                return !in_array($course['id'], $enrolledCourseIds);
            });
            $data['availableCourses'] = array_values($availableCourses);

            // Dummy data for other sections (can be updated later)
            $data['upcomingDeadlines'] = [
                ['course' => 'Web Development', 'assignment' => 'Final Project', 'due_date' => '2025-01-25', 'status' => 'pending'],
            ];
            $data['recentGrades'] = [
                ['course' => 'Web Development', 'assignment' => 'HTML/CSS Project', 'grade' => 95, 'date' => '2025-01-20'],
            ];
        }

        return view('courses/index', $data);
    }

    private function generateCourseNumber($title)
    {
        // Clean and process the title
        $cleanTitle = strtoupper(trim($title));
        $cleanTitle = preg_replace('/[^A-Z\s]/', '', $cleanTitle); // Remove non-letter characters except spaces
        $words = array_filter(explode(' ', $cleanTitle)); // Split into words and remove empty entries

        // Generate acronym based on word count
        if (count($words) >= 3) {
            // Take first 3 words' first letters plus a number
            $acronym = substr($words[0], 0, 1) . substr($words[1], 0, 1) . substr($words[2], 0, 1) . rand(1, 9);
        } elseif (count($words) === 2) {
            // Take first 2 words' first letters plus 2-digit number
            $acronym = substr($words[0], 0, 1) . substr($words[1], 0, 1) . rand(10, 99);
        } elseif (count($words) === 1) {
            // Take first 3 letters of single word
            $acronym = substr($words[0], 0, 3);
        } else {
            // Fallback
            $acronym = 'CN' . rand(100, 999);
        }

        // Ensure uniqueness by checking database
        $courseModel = new \App\Models\CourseModel();
        $originalAcronym = $acronym;
        $counter = 1;

        while ($courseModel->where('course_number', $acronym)->countAllResults() > 0) {
            $acronym = $originalAcronym . $counter;
            $counter++;
            if ($counter > 99) { // Prevent infinite loop
                $acronym = $originalAcronym . rand(100, 999);
                break;
            }
        }

        return $acronym;
    }
}
