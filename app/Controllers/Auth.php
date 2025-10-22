<?php

namespace App\Controllers;

class Auth extends BaseController
{
    
    public function register()
    {
        $session = session();
        if ($session->get('isLoggedIn')) {
            return $this->redirectBasedOnRole($session->get('userRole'));
        }

        // Process form submission (POST)
        if ($this->request->getMethod() === 'POST') {
            $name = trim((string) $this->request->getPost('name'));
            $email = trim((string) $this->request->getPost('email'));
            $password = (string) $this->request->getPost('password');
            $passwordConfirm = (string) $this->request->getPost('password_confirm');

            if ($name === '' || $email === '' || $password === '' || $passwordConfirm === '') {
                return redirect()->back()->withInput()->with('register_error', 'All fields are required.');
            }

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->withInput()->with('register_error', 'Invalid email address.');
            }

            if ($password !== $passwordConfirm) {
                return redirect()->back()->withInput()->with('register_error', 'Passwords do not match.');
            }

            $userModel = new \App\Models\UserModel();

            if ($userModel->where('email', $email)->first()) {
                return redirect()->back()->withInput()->with('register_error', 'Email is already registered.');
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $userId = $userModel->insert([
                'name' => $name,
                'email' => $email,
                'role' => 'student',
                'password' => $passwordHash,
            ], true);

            if (! $userId) {
                return redirect()->back()->withInput()->with('register_error', 'Registration failed.');
            }

            // Notify all admins about the new registration
            $adminUsers = $userModel->where('role', 'admin')->findAll();
            $notificationModel = new \App\Models\NotificationModel();
            foreach ($adminUsers as $admin) {
                $notificationModel->insert([
                    'user_id' => $admin['id'],
                    'message' => "New user {$name} ({$email}) has registered.",
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            return redirect()
                ->to(base_url('login'))
                ->with('register_success', 'Account created successfully. Please log in.');
        }

        return view('auth/register');
    }

    public function login()
    {
        $session = session();

        if ($session->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }
         // Process form submission (POST)
           if ($this->request->getMethod() === 'POST') {
               $email = trim((string) $this->request->getPost('email'));
               $password = (string) $this->request->getPost('password');
       
               $userModel = new \App\Models\UserModel();
               $user = $userModel->where('email', $email)->first();
               
               if ($user && password_verify($password, $user['password'])) {
                   // Store the user's email and role in the session
                   $session->set([
                       'isLoggedIn' => true,
                       'userEmail' => $email,
                       'userRole' => $user['role'],
                       'user_id' => $user['id'],
                   ]);

                   // Redirect to unified dashboard
                   return redirect()->to(base_url('dashboard'));
               }
       
               return redirect()->back()->with('login_error', 'Invalid credentials');
           }
       
           return view('auth/login');
       }

 //Logout
    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to(base_url('login'));
    }

  // Handles the dashboard each role admin, teacher, student
        public function dashboard()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = $session->get('userRole');
        $data = [
            'userRole' => $role,
            'userEmail' => $session->get('userEmail')
        ];

        if ($role === 'admin') {
            $userModel = new \App\Models\UserModel();
            $courseModel = new \App\Models\CourseModel();

            $data['totalUsers'] = $userModel->countAllResults();
            $data['courseCount'] = $courseModel->countAllResults();

            // Get all courses for the course table
            $courses = $courseModel->findAll();
            $data['courses'] = $courses;

            // Recent activity
            $data['recentActivities'] = [
                ['name'=>'Jane Smith','role'=>'Teacher','action'=>'Added','target'=>'New Course: "Math 101"','created_at'=>'2025-09-21 09:50'],
            ];
        } elseif ($role === 'teacher') {
            $courseModel = new \App\Models\CourseModel();
            $enrollmentModel = new \App\Models\EnrollmentModel();
            $user_id = $session->get('user_id');

            // Get courses taught by this teacher
            $courses = $courseModel->where('instructor_id', $user_id)->findAll();

            // Add student count and status to each course
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

            // Fetch real notifications for the teacher
            $notificationModel = new \App\Models\NotificationModel();
            $data['notifications'] = $notificationModel->getNotificationsForUser($user_id);
            $data['unreadCount'] = $notificationModel->getUnreadCount($user_id);
        } elseif ($role === 'student') {
            $enrollmentModel = new \App\Models\EnrollmentModel();
            $courseModel = new \App\Models\CourseModel();
            $announcementModel = new \App\Models\AnnouncementModel();
            $user_id = $session->get('user_id');

            // Get enrolled courses
            $enrolledCourses = $enrollmentModel->getUserEnrollments($user_id);
            $data['enrolledCourses'] = $enrolledCourses;

            // Get all courses
            $allCourses = $courseModel->findAll();

            // Get enrolled course IDs
            $enrolledCourseIds = array_column($enrolledCourses, 'course_id');

            // Get available courses (not enrolled)
            $availableCourses = array_filter($allCourses, function($course) use ($enrolledCourseIds) {
                return !in_array($course['id'], $enrolledCourseIds);
            });
            $data['availableCourses'] = array_values($availableCourses);

            // Get announcements for dashboard
            $data['announcements'] = $announcementModel->orderBy('created_at', 'DESC')->findAll();

            // Dummy data for other sections (can be updated later)
            $data['upcomingDeadlines'] = [
                ['course' => 'Web Development', 'assignment' => 'Final Project', 'due_date' => '2025-01-25', 'status' => 'pending'],
            ];
            $data['recentGrades'] = [
                ['course' => 'Web Development', 'assignment' => 'HTML/CSS Project', 'grade' => 95, 'date' => '2025-01-20'],
            ];
        }

        return view('auth/dashboard', $data);
    }
}
