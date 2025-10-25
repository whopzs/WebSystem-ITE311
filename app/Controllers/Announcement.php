<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;

class Announcement extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');
        $userRole = session()->get('userRole');
        
        if ($userRole === 'teacher') {
            return redirect()->to('/dashboard');
        }

        $announcementModel = new AnnouncementModel();
        $courseModel = new CourseModel();
        $enrollmentModel = new EnrollmentModel();

        if ($userRole === 'student') {
            // Students see announcements for their enrolled courses
            $enrolledCourses = $enrollmentModel->where('user_id', $userId)->findAll();
            $courseIds = array_column($enrolledCourses, 'course_id');
            if (!empty($courseIds)) {
                $data['announcements'] = $announcementModel->whereIn('course_id', $courseIds)
                    ->orderBy('created_at', 'DESC')->findAll();
            } else {
                $data['announcements'] = [];
            }
        } else {
            $data['announcements'] = $announcementModel->orderBy('created_at', 'DESC')->findAll();
        }
        foreach ($data['announcements'] as &$announcement) {
            if (isset($announcement['course_id']) && $announcement['course_id']) {
                $course = $courseModel->find($announcement['course_id']);
                $announcement['course_title'] = $course ? $course['title'] : 'Unknown Course';
            } else {
                $announcement['course_title'] = 'General';
            }
        }

        return view('announcements/index', $data);
    }

    public function create()
    {
        $userId = session()->get('user_id');
        $userRole = session()->get('userRole');

        if ($userRole !== 'teacher') {
            return redirect()->to('/dashboard')->with('error', 'Only teachers can create announcements.');
        }

        $courseModel = new CourseModel();
        $data['courses'] = $courseModel->where('instructor_id', $userId)->findAll();

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'title' => 'required|min_length[3]|max_length[255]',
                'content' => 'required|min_length[10]',
                'course_id' => 'required|integer'
            ];

            if ($this->validate($rules)) {
                $announcementModel = new AnnouncementModel();

                $announcementData = [
                    'title' => $this->request->getPost('title'),
                    'content' => $this->request->getPost('content'),
                    'course_id' => $this->request->getPost('course_id'),
                    'user_id' => $userId
                ];
                session()->set('announcement_course_id', $this->request->getPost('course_id'));

                $announcementModel->save($announcementData);

                return redirect()->to('/announcements')->with('success', 'Announcement created successfully!');
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('announcements/create', $data);
    }
}
