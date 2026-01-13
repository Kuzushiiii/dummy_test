<?php

namespace App\Models;

use CodeIgniter\Model;

class MUser extends Model
{
    protected $dbs;
    protected $table = 'msuser as us';
    public function __construct()
    {
        $this->dbs = db_connect();
        $this->builder = $this->dbs->table($this->table);
    }

    public function searchable()
    {
        return [
            null,
            "us.username",
            "us.fullname",
            "us.email",
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
        return $this->builder->where("lower(username)", strtolower($name))->get()->getRowArray();
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
        return $this->builder->update($data, ['id' => $id]);
    }

    public function destroy($column, $value)
    {
        return $this->builder->delete([$column => $value]);
    }
}
