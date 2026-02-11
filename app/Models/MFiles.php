<?php

namespace App\Models;

use CodeIgniter\Model;

class MFiles extends Model
{
    protected $table      = 'msfiles';
    protected $primaryKey = 'fileid';
    protected $allowedFields = [
        'filename',
        'filerealname',
        'filedirectory',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function searchable()
    {
        return [
            null,            // 0 - No
            'filerealname',  // 1 - Nama asli file
            'filename',      // 2 - Nama file di server
            null,            // 3 - Aksi
        ];
    }

    public function datatable()
    {
        return $this->db->table($this->table);
    }
}
