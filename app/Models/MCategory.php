<?php

namespace App\Models;

use CodeIgniter\Model;

class MCategory extends Model
{
    protected $table = 'mscategory as ct';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['categoryname', 'description', 'filepath'];

    public function __construct()
    {
        parent::__construct();
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
        return $this->builder->where("lower(ct.categoryname)", strtolower($name))->get()->getRowArray();
    }

    public function getOne($categoryid)
    {
        return $this->builder->where("id", $categoryid)->get()->getRowArray();
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