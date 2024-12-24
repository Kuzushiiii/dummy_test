<?php

namespace App\Models;

use CodeIgniter\Model;

class MDocument extends Model
{
    protected $table = 'msdocument as md';
    protected $dbs;
    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table($this->table);
    }


    public function searchable()
    {
        return [
            null,
            'md.documentname',
            'md.description',
            'md.filepath',
            null,
            null,

        ];
    }

    public function datatable()
    {
        return $this->builder;
    }
    public function getByName($name)
    {
        return $this->builder->where("lower(documentname)", strtolower($name))->get()->getRowArray();
    }

    public function getOne($userid)
    {
        return $this->builder->where("id", $userid)->get()->getRowArray();
    }

    public function store($data)
    {
        return $this->builder->insert($data);
    }

    public function edit($data, $id)
    {
        $this->builder->where('id', $id);
        $result = $this->builder->update($data);
    
        // Periksa apakah pembaruan berhasil
        if ($result) {
            $affectedRows = $this->db->affectedRows();
            return $affectedRows > 0; // True jika ada baris yang diperbarui
        }
    
        return false; // Jika query gagal
    }
    

    public function destroy($column, $value)
    {
        return $this->builder->delete([$column => $value]);
    }
}
