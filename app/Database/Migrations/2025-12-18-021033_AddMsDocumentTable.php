<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMsDocumentTable extends Migration
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
            'documentname' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'description' => [
                'type' => 'TEXT',
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
        $this->forge->createTable('msdocument');
    }

    public function down()
    {
        $this->forge->dropTable('msdocument');
    }
}
