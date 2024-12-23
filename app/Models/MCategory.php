<?php

namespace App\Models;

use CodeIgniter\Model;

class MCategory extends Model
{
    protected $db;
    protected $table = 'mscategory as ct';
    public function __construct()
    {
        $this->db = db_connect();
        $this->builder = $this->db->table($this->table);
    }

    public function searchable()
    {
        return [
            null,
            "ct.categoryname",
            "ct.description",
            "ct.filepath",
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
        return $this->builder->where("lower(customername)", strtolower($name))->get()->getRowArray();
    }

    public function getOne($customerid)
    {
        return $this->builder->where("id", $customerid)->get()->getRowArray();
    }

    public function store($data)
    {
        return $this->builder->insert($data);
    }

    public function edit($data, $id)
    {
        return $this->builder->update($data, ['id' => $id]);
    }

    public function destroy($column, $value)
    {
        return $this->builder->delete([$column => $value]);
    }
    
}
