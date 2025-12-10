<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToEnrollmentsTable extends Migration
{
    public function up()
    {
        // Check if column already exists
        $fields = $this->db->getFieldData('enrollments');
        $columnExists = false;
        foreach ($fields as $field) {
            if ($field->name === 'status') {
                $columnExists = true;
                break;
            }
        }
        
        if (!$columnExists) {
            $fields = [
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['pending', 'approved', 'rejected'],
                    'default' => 'pending',
                    'null' => false,
                ],
            ];

            $this->forge->addColumn('enrollments', $fields);
        }
        
        // Update all existing enrollments to 'approved' status
        // This ensures existing enrollments remain valid
        try {
            $this->db->table('enrollments')->update(['status' => 'approved']);
        } catch (\Exception $e) {
            // If update fails, it's okay - column might have just been added
        }
    }

    public function down()
    {
        $this->forge->dropColumn('enrollments', 'status');
    }
}

