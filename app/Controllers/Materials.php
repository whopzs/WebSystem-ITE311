<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;

class Materials extends BaseController
{
    public function upload($course_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $userRole = $session->get('userRole');
        $userId = $session->get('user_id');

        $courseModel = new CourseModel();
        $course = $courseModel->find($course_id);

        if (!$course) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Course not found');
        }

        if ($userRole !== 'admin' && $course['instructor_id'] != $userId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied');
        }

        if ($this->request->getMethod() === 'POST') {
            // Load validation library
            $validation = \Config\Services::validation();

            // Set validation rules
            $validation->setRules([
                'material_file' => [
                    'label' => 'File',
                    'rules' => 'uploaded[material_file]|max_size[material_file,10240]|ext_in[material_file,pdf,ppt]'
                ]
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                return redirect()->back()->withInput()->with('upload_error', $validation->getError('material_file'));
            }

            // Handle file upload
            $file = $this->request->getFile('material_file');

            if (strtolower($file->getExtension()) === 'mp4') {
                return redirect()->back()->withInput()->with('upload_error', 'MP4 files are not allowed.');
            }

            if ($file->isValid() && !$file->hasMoved()) {
                // Generate unique filename
                $newName = $file->getRandomName();

                // Move file to uploads/materials directory
                $file->move('uploads/materials', $newName);

                // Save to database
                $materialModel = new MaterialModel();
                $data = [
                    'course_id' => $course_id,
                    'file_name' => $file->getClientName(),
                    'file_path' => 'uploads/materials/' . $newName
                ];

                if ($materialModel->insertMaterial($data)) {
                    // Notify all enrolled students about the new material
                    $enrollmentModel = new EnrollmentModel();
                    $enrollments = $enrollmentModel->where('course_id', $course_id)->findAll();
                    $notificationModel = new \App\Models\NotificationModel();
                    foreach ($enrollments as $enrollment) {
                        $notificationModel->insert([
                            'user_id' => $enrollment['user_id'],
                            'message' => "New material available for course: {$course['title']}",
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }

                    return redirect()->back()->with('upload_success', 'Material uploaded successfully');
                } else {

                    if (file_exists('uploads/materials/' . $newName)) {
                        unlink('uploads/materials/' . $newName);
                    }
                    return redirect()->back()->with('upload_error', 'Failed to save material to database');
                }
            } else {
                return redirect()->back()->with('upload_error', 'File upload failed');
            }
        }

        // Get existing materials for this course
        $materialModel = new MaterialModel();
        $existingMaterials = $materialModel->getMaterialsByCourse($course_id);

        $data = [
            'course' => $course,
            'userRole' => $userRole,
            'existingMaterials' => $existingMaterials
        ];

        return view('materials/upload', $data);
    }

    public function delete($material_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $userRole = $session->get('userRole');
        $userId = $session->get('user_id');

        $materialModel = new MaterialModel();
        $material = $materialModel->find($material_id);

        if (!$material) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Material not found');
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($material['course_id']);

        if ($userRole !== 'admin' && $course['instructor_id'] != $userId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied');
        }

        if ($materialModel->deleteMaterial($material_id)) {
            return redirect()->to(base_url('admin/course/' . $material['course_id'] . '/upload'))->with('delete_success', 'Material deleted successfully');
        } else {
            return redirect()->to(base_url('admin/course/' . $material['course_id'] . '/upload'))->with('delete_error', 'Failed to delete material');
        }
    }

    public function download($material_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $userId = $session->get('user_id');

        $materialModel = new MaterialModel();
        $material = $materialModel->find($material_id);

        if (!$material) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Material not found');
        }

        $enrollmentModel = new EnrollmentModel();
        $isEnrolled = $enrollmentModel->isAlreadyEnrolled($userId, $material['course_id']);

        if (!$isEnrolled) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied. You are not enrolled in this course.');
        }

        if (!file_exists($material['file_path'])) {
            return redirect()->to(base_url('dashboard'))->with('error', 'File not found on server');
        }

        return $this->response->download($material['file_path'], null, true)->setFileName($material['file_name']);
    }


}
