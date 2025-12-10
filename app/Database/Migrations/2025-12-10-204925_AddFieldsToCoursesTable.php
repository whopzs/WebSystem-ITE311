<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToCoursesTable extends Migration
{
    public function up()
    {
        $fields = [
            'course_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'semester' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'term' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'academic_year' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'day' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'time' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'room' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        ];

        $this->forge->addColumn('courses', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', ['course_number', 'semester', 'term', 'academic_year', 'day', 'time', 'room']);
    }
}
