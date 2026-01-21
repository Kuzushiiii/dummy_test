<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMsUserTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'unique'     => true,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'fullname' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'telp' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'createddate' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'createdby' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'updateddate' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updatedby' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('msuser');
    }

    public function down()
    {
        $this->forge->dropTable('msuser');
    }
}
