<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMsProductTable extends Migration
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
            'productname' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'stock' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
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
        $this->forge->createTable('msproduct');
    }

    public function down()
    {
        $this->forge->dropTable('msproduct');
    }
}
