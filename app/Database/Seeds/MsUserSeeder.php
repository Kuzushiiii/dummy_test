<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class MsUserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'username'   => 'admin',
            'password'   => password_hash('admin1234', PASSWORD_DEFAULT), // bcrypt 10-rounder && 2y-identifier
            'fullname'   => 'System Administrator',
            'email'      => 'admin@gmail.com',
            'telp'       => '081234567890',
            'createddate' => Time::now(),
            'createdby' => null,
            'updateddate' => Time::now(),
            'updatedby' => null,
        ];

        $this->db->table('msuser')->insert($data);
    }
}
