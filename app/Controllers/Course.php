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
            'enrollment_date' => date('Y-m-d H:i:s')
        ];

        if ($enrollmentModel->enrollUser($data)) {
            // Get the enrollment date from the data
            $enrollment_date = $data['enrollment_date'];

            // Create notification for successful enrollment for the student
            $notificationModel = new \App\Models\NotificationModel();
            $notificationData = [
                'user_id' => $user_id,
                'message' => "You have been enrolled in {$course['title']}",
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $notificationModel->insert($notificationData);

            // Notify the teacher about the new enrollment
            $userModel = new \App\Models\UserModel();
            $student = $userModel->find($user_id);
            $studentName = $student['name'];
            $teacherNotification = [
                'user_id' => $course['instructor_id'],
                'message' => "Student {$studentName} has enrolled in your course: {$course['title']}",
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $notificationModel->insert($teacherNotification);

            $materialModel = new \App\Models\MaterialModel();
            $materials = $materialModel->getMaterialsByCourse($course_id);

            return $this->response->setJSON(['success' => true, 'message' => 'Successfully enrolled in the course', 'enrollment_date' => $enrollment_date, 'materials' => $materials]);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Failed to enroll']);
        }
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

        $data = [
            'title' => $this->request->getPost('title'),
            'course_number' => $this->request->getPost('course_number'),
            'description' => $this->request->getPost('description'),
            'semester' => $this->request->getPost('semester'),
            'term' => $this->request->getPost('term'),
            'academic_year' => $this->request->getPost('academic_year'),
            'instructor_id' => $this->request->getPost('assigned_teacher'),
        ];

        $courseId = $courseModel->insert($data);

        if ($courseId) {
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

        $teacherId = $this->request->getPost('teacher_id');
        $day = $this->request->getPost('day');
        $time = $this->request->getPost('time');
        $room = $this->request->getPost('room');

        if (!$teacherId) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Teacher ID is required']);
        }

        $data = [
            'day' => $day,
            'time' => $time,
            'room' => $room,
        ];

        // Update all courses by the teacher with the schedule
        $updated = $courseModel->where('instructor_id', $teacherId)->set($data)->update();

        if ($updated) {
            return $this->response->setJSON(['success' => true, 'message' => 'Schedule updated successfully for teacher\'s courses']);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Failed to update schedule']);
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

            $data['totalUsers'] = $userModel->countAllResults();
            $data['courseCount'] = $courseModel->countAllResults();

            $courses = $courseModel->findAll();
            $data['courses'] = $courses;

            $data['teachers'] = $userModel->where('role', 'teacher')->findAll();

            // Recent activity
            $data['recentActivities'] = [
                ['name'=>'Jane Smith','role'=>'Teacher','action'=>'Added','target'=>'New Course: "Math 101"','created_at'=>'2025-09-21 09:50'],
            ];
        } elseif ($role === 'teacher') {
            $courseModel = new \App\Models\CourseModel();
            $enrollmentModel = new \App\Models\EnrollmentModel();
            $user_id = $session->get('user_id');

            $courses = $courseModel->where('instructor_id', $user_id)->findAll();

            $teacherCourses = [];
            foreach ($courses as $course) {
                $studentCount = $enrollmentModel->where('course_id', $course['id'])->countAllResults();
                $teacherCourses[] = [
                    'id' => $course['id'],
                    'title' => $course['title'],
                    'students' => $studentCount,
                    'status' => 'active'
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

            // Get enrolled courses
            $enrolledCourses = $enrollmentModel->getUserEnrollments($user_id);
            $data['enrolledCourses'] = $enrolledCourses;

            // Get all courses
            $allCourses = $courseModel->findAll();

            $enrolledCourseIds = array_column($enrolledCourses, 'course_id');

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
}
