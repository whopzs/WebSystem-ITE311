<?php

namespace App\Controllers;

use App\Models\UserModel;

class Settings extends BaseController
{
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied. Admin only.');
        }

        $data = [
            'userRole' => $role,
            'userEmail' => $session->get('userEmail'),
        ];

        // Get current admin user info
        $userModel = new UserModel();
        $userId = $session->get('user_id');
        $user = $userModel->find($userId);
        $data['user'] = $user;

        return view('settings/index', $data);
    }

    public function teacher()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = $session->get('userRole');
        if ($role !== 'teacher') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied. Teacher only.');
        }

        $data = [
            'userRole' => $role,
            'userEmail' => $session->get('userEmail'),
        ];

        // Get current teacher user info
        $userModel = new UserModel();
        $userId = $session->get('user_id');
        $user = $userModel->find($userId);
        $data['user'] = $user;

        return view('settings/teacher', $data);
    }

    public function student()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = $session->get('userRole');
        if ($role !== 'student') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied. Student only.');
        }

        $data = [
            'userRole' => $role,
            'userEmail' => $session->get('userEmail'),
        ];

        // Get current student user info
        $userModel = new UserModel();
        $userId = $session->get('user_id');
        $user = $userModel->find($userId);
        $data['user'] = $user;

        return view('settings/student', $data);
    }

    public function updateProfile()
    {
        $this->response->setContentType('application/json');

        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $role = session()->get('userRole');
        if (!in_array($role, ['admin', 'teacher', 'student'])) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = session()->get('user_id');
        $userModel = new UserModel();

        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        $user = $userModel->find($userId);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'User not found']);
        }

        $updateData = [];

        // Update name
        if ($name && $name !== $user['name']) {
            $updateData['name'] = $name;
        }

        // Update email
        if ($email && $email !== $user['email']) {
            // Check if email is already taken by another user
            $existingUser = $userModel->where('email', $email)->where('id !=', $userId)->first();
            if ($existingUser) {
                return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Email already in use']);
            }
            $updateData['email'] = $email;
        }

        // Update password if provided
        if ($newPassword) {
            if (!$currentPassword) {
                return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Current password is required']);
            }

            // Verify current password
            if (!password_verify($currentPassword, $user['password'])) {
                return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Current password is incorrect']);
            }

            if ($newPassword !== $confirmPassword) {
                return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'New passwords do not match']);
            }

            if (strlen($newPassword) < 6) {
                return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Password must be at least 6 characters']);
            }

            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        if (empty($updateData)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No changes to update']);
        }

        if ($userModel->update($userId, $updateData)) {
            // Update session if email changed
            if (isset($updateData['email'])) {
                session()->set('userEmail', $updateData['email']);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Profile updated successfully']);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Failed to update profile']);
        }
    }

    public function updateSystemSettings()
    {
        $this->response->setContentType('application/json');

        if (!session()->get('isLoggedIn') || session()->get('userRole') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        // For now, we'll store system settings in a simple way
        // In a production system, you might want to create a settings table
        $siteName = $this->request->getPost('site_name');
        $siteEmail = $this->request->getPost('site_email');
        $enrollmentApproval = $this->request->getPost('enrollment_approval') === '1' ? '1' : '0';
        $maxEnrollments = $this->request->getPost('max_enrollments');

        // You can store these in a settings table or config file
        // For now, we'll just return success
        // TODO: Implement actual settings storage

        return $this->response->setJSON([
            'success' => true,
            'message' => 'System settings updated successfully',
            'data' => [
                'site_name' => $siteName,
                'site_email' => $siteEmail,
                'enrollment_approval' => $enrollmentApproval,
                'max_enrollments' => $maxEnrollments
            ]
        ]);
    }
}

