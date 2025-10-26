<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\UserModel;

class Announcement extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');
        $userRole = session()->get('userRole');

        $announcementModel = new AnnouncementModel();
        $courseModel = new CourseModel();
        $enrollmentModel = new EnrollmentModel();
        $userModel = new UserModel();

        if ($userRole === 'student') {
            $enrolledCourses = $enrollmentModel->where('user_id', $userId)->findAll();
            $courseIds = array_column($enrolledCourses, 'course_id');

            $studentAnnouncements = [];
            if (!empty($courseIds)) {
                // Get student announcements for their enrolled courses
                $enrolledStudentAnnouncements = $announcementModel->whereIn('course_id', $courseIds)->where('role', 'student')->orderBy('created_at', 'DESC')->findAll();
                $generalStudentAnnouncements = $announcementModel->where('course_id', null)->where('role', 'student')->orderBy('created_at', 'DESC')->findAll();
                $studentAnnouncements = array_merge($enrolledStudentAnnouncements, $generalStudentAnnouncements);
            } else {
                $studentAnnouncements = $announcementModel->where('course_id', null)->where('role', 'student')->orderBy('created_at', 'DESC')->findAll();
            }

            $courseAnnouncements = [];
            if (!empty($courseIds)) {
                $courseAnnouncements = $announcementModel->whereIn('course_id', $courseIds)->orderBy('created_at', 'DESC')->findAll();
            }

            $data['announcements'] = array_merge($studentAnnouncements, $courseAnnouncements);
            usort($data['announcements'], function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
        } elseif ($userRole === 'teacher') {
            $teacherAnnouncements = $announcementModel->where('role', 'teacher')->orderBy('created_at', 'DESC')->findAll();
            $data['announcements'] = $teacherAnnouncements;
        } else {
            $data['announcements'] = $announcementModel->where('role IS NOT NULL')->orderBy('created_at', 'DESC')->findAll();
        }
        foreach ($data['announcements'] as &$announcement) {
            if (isset($announcement['course_id']) && $announcement['course_id']) {
                $course = $courseModel->find($announcement['course_id']);
                $announcement['course_title'] = $course ? $course['title'] : 'Unknown Course';
            }

            // Get author information
            if (isset($announcement['user_id']) && $announcement['user_id']) {
                $author = $userModel->find($announcement['user_id']);
                if ($author) {
                    if ($author['role'] === 'admin') {
                        $announcement['author_name'] = 'Administrator';
                    } else {
                        $announcement['author_name'] = $author['name'];
                    }
                }
            }
        }
        return view('announcements/index', $data);
    }

    public function create()
    {
        $userId = session()->get('user_id');
        $userRole = session()->get('userRole');

        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Only teachers and admins can create announcements.');
        }

        $courseModel = new CourseModel();
        if ($userRole === 'teacher') {
            $data['courses'] = $courseModel->where('instructor_id', $userId)->findAll();
            $data['isGeneral'] = false;
        } else {
            $data['courses'] = $courseModel->findAll();
            $data['isGeneral'] = true;
        }
        $data['userRole'] = $userRole;

        if ($this->request->getMethod() === 'POST') {
            if ($userRole === 'teacher') {
                $rules = [
                    'title' => 'required|min_length[3]|max_length[255]',
                    'content' => 'required|min_length[10]',
                    'course_id' => 'required|integer'
                ];
            } else {
                $rules = [
                    'title' => 'required|min_length[3]|max_length[255]',
                    'content' => 'required|min_length[10]',
                    'role' => 'required|in_list[teacher,student]'
                ];
            }

            if ($this->validate($rules)) {
                
                $announcementModel = new AnnouncementModel();

                $announcementData = [
                    'title' => $this->request->getPost('title'),
                    'content' => $this->request->getPost('content'),
                    'course_id' => $userRole === 'teacher' ? $this->request->getPost('course_id') : null,
                    'user_id' => $userId,
                    'role' => $userRole === 'admin' ? $this->request->getPost('role') : null
                ];

                $announcementModel->save($announcementData);

                return redirect()->to('/announcements/create')->with('success', 'Announcement created successfully!');
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('announcements/create', $data);
    }
}
