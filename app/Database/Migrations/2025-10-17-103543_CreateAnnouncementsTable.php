<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnnouncementsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'content' => [
                'type' => 'TEXT'
            ],
            'course_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true
            ],
            'user_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true
            ],
            'role' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('course_id','courses','id','CASCADE','CASCADE');
        $this->forge->addForeignKey('user_id','users','id','CASCADE','CASCADE');
        $this->forge->createTable('announcements');
    }

    public function down()
    {
        $this->forge->dropTable('announcements');
    }
}
