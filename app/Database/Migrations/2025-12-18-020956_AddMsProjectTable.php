<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMsProjectTable extends Migration
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
            'projectname' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'startdate' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'enddate' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'filepath' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
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
        $this->forge->createTable('msproject');
    }

    public function down()
    {
        $this->forge->dropTable('msproject');
    }
}
