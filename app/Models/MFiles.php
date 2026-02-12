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
        'created_date',
        'created_by',
        'update_date',
        'update_by'

    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function searchable()
    {
        return [
            null,            // 0 - No
            'filerealname',      // 1 - File Name
            'filedirectory', // 2 - File Path
            null,    // 3 - Created At
            'created_by',    // 4 - Created By
            null,            // 5 - Actions
        ];
    }

    public function datatable()
    {
        // join ke tabel user agar bisa ambil nama pembuat & pengupdate
        return $this->db->table($this->table . ' f')
            ->select('f.*, u.fullname AS created_by_name, uu.fullname AS updated_by_name')
            ->join('msuser u', 'u.id = f.created_by', 'left')
            ->join('msuser uu', 'uu.id = f.update_by', 'left');
    }
}
