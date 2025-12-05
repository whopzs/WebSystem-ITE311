<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $allowedFields = ['name', 'email', 'role', 'password', 'status'];

    protected $returnType = 'array';

    public function updateUser($id, $data)
    {
        return $this->update($id, $data);
    }
}
