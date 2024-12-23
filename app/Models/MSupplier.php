<?php

namespace App\Models;

use CodeIgniter\Model;

class MSupplier extends Model
{
    protected $table = 'mssupplier';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'suppliername',
        'address',
        'phone',
        'email',
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
        if (!$this->builder->insert($data)) {
            log_message('error', 'Database Error: ' . json_encode($this->dbs->error()));
            return false;
        }
        return true;
    }


    public function searchable()
    {
        return [
            null,
            "suppliername",
            "address",
            "phone",
            "email",
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
        return $this->builder->where("lower(suppliername)", strtolower($name))->get()->getRowArray();
    }

    public function getOneBy($column, $value)
    {
        return $this->builder->where($column, $value)->get()->getRowArray();
    }

}