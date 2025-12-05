<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'name'        => ['type'=>'VARCHAR','constraint'=>'100'],
            'email'       => ['type'=>'VARCHAR','constraint'=>'100','unique'=>true],
            'role'        => ['type'=>'ENUM','constraint'=>['admin','teacher','student'],'default'=>'admin'],
            'password'    => ['type'=>'VARCHAR','constraint'=>'255'],
            'status'      => ['type'=>'VARCHAR','constraint'=>'20','null'=>true,'default'=>'active'],
            'deleted_at'  => ['type'=>'DATETIME','null'=>true],
            'created_at'  => ['type'=>'DATETIME','null'=>true],
            'updated_at'  => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
