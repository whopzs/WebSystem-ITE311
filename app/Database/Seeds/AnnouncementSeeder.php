<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Welcome to the New Semester!',
                'content' => 'We are excited to welcome all students to the new semester. Please check your course schedules and ensure you have all required materials ready for the first day of classes.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('announcements')->insertBatch($data);
    }
}
