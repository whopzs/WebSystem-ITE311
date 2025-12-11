<?php

namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    public function index()
    {
        $userRole = session()->get('userRole');

        if ($userRole !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Admin only.');
        }

        $userModel = new UserModel();

        $currentUserId = session()->get('user_id');

        $query = $userModel->where('(role != "admin" OR (role = "admin" AND id != ' . $currentUserId . '))');

        // Get search parameters
        $searchName = $this->request->getGet('name');
        $searchEmail = $this->request->getGet('email');
        $searchRole = $this->request->getGet('role');

        if (!empty($searchName)) {
            $query->like('name', $searchName);
        }
        if (!empty($searchEmail)) {
            $query->like('email', $searchEmail);
        }
        if (!empty($searchRole) && $searchRole !== 'all') {
            $query->where('role', $searchRole);
        }

        $data['users'] = $query->findAll();

        $data['searchName'] = $searchName;
        $data['searchEmail'] = $searchEmail;
        $data['searchRole'] = $searchRole;

        return view('users/index', $data);
    }

    public function update($id = null)
    {
        $userRole = session()->get('userRole');

        if ($userRole !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);
        if (!$user || $user['role'] === 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot modify admin users.']);
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name' => 'required',
                'email' => 'required|valid_email|is_unique[users.email,id,' . $id . ']'
            ];

            if ($this->validate($rules)) {
                $userModel = new UserModel();

                $data = [
                    'name' => $this->request->getPost('name'),
                    'email' => $this->request->getPost('email')
                ];

                if ($userModel->updateUser($id, $data)) {
                    return $this->response->setJSON(['success' => true, 'message' => 'User updated successfully.']);
                } else {
                    return $this->response->setJSON(['success' => false, 'message' => 'Failed to update user.']);
                }
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Validation errors.', 'errors' => $this->validator->getErrors()]);
            }
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request.']);
    }

    public function create()
    {
        $userRole = session()->get('userRole');

        if ($userRole !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name' => 'required',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'role' => 'required|in_list[admin,teacher,student]'
            ];

            if ($this->validate($rules)) {
                $userModel = new UserModel();

                $data = [
                    'name' => $this->request->getPost('name'),
                    'email' => $this->request->getPost('email'),
                    'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'role' => $this->request->getPost('role')
                ];

                if ($userModel->insert($data)) {
                    return $this->response->setJSON(['success' => true, 'message' => 'User created successfully.']);
                } else {
                    return $this->response->setJSON(['success' => false, 'message' => 'Failed to create user.']);
                }
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Validation errors.', 'errors' => $this->validator->getErrors()]);
            }
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request.']);
    }

    public function delete($id = null)
    {
        $userRole = session()->get('userRole');

        if ($userRole !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);
        if (!$user || $user['role'] === 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot delete admin users.']);
        }

        if ($userModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'User deleted successfully.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete user.']);
        }
    }
}
