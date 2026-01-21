<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMsCustomerTable extends Migration
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
            'customername' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
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
        $this->forge->createTable('mscustomer');
    }

    public function down()
    {
        $this->forge->dropTable('mscustomer');
    }
}
