<?php

namespace App\Models;

use CodeIgniter\Model;

class MProduct extends Model
{
    protected $dbs;
    protected $table = 'msproduct';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'productname',
        'category',
        'price',
        'stock',
        'filepath',
        'createddate',
        'createdby',
        'updateddate',
        'updatedby'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->dbs = db_connect();
        $this->builder = $this->dbs->table($this->table);
    }

    public function store($data)
    {
        return $this->builder->insert($data);
    }

    public function searchable()
    {
        return [
            null,
            "productname",
            "category",
            "price",
            "stock",
            null,
            null,
            null,
        ];
    }

    public function datatable()
    {
        return $this->builder;
    }

    public function getOne($id)
    {
        return $this->builder->where('id', $id)->get()->getRowArray();
    }

    public function edit($data, $id)
    {
        return $this->builder->update($data, ['id' => $id]);
    }

    public function destroy($column, $value)
    {
        return $this->builder->delete([$column => $value]);
    }

    public function getByName($name)
    {
        return $this->builder->where("lower(productname)", strtolower($name))->get()->getRowArray();
    }

    public function getOneBy($column, $value)
    {
        return $this->builder->where($column, $value)->get()->getRowArray();
    }

}