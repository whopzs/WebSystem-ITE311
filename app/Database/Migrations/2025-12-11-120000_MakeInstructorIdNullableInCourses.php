<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeInstructorIdNullableInCourses extends Migration
{
    public function up()
    {
        // Modify instructor_id to allow NULL values
        // Note: If you encounter foreign key constraint errors, you may need to:
        // 1. Drop the foreign key constraint first
        // 2. Modify the column
        // 3. Re-add the foreign key constraint
        $fields = [
            'instructor_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
        ];

        $this->forge->modifyColumn('courses', $fields);
    }

    public function down()
    {
        // Revert instructor_id to NOT NULL
        $fields = [
            'instructor_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
        ];

        $this->forge->modifyColumn('courses', $fields);
    }
}

