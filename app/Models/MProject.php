<?php

namespace App\Models;

use CodeIgniter\Model;

class MProject extends Model
{
    protected $table = 'msproject as p';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    
    public function __construct()
    {
        $this->dbs = db_connect();
        $this->builder = $this->dbs->table($this->table);
    }

    protected $allowedFields = [
        'projectname', 
        'description', 
        'startdate', 
        'enddate', 
        'createddate', 
        'createdby', 
        'updateddate', 
        'updatedby'
    ];
    

    public function searchable()
    {
        return [
            null,
            "p.projectname",
            "p.description",
            "p.startdate",
            "p.enddate",
            null,
        ];
    }
    

    public function datatable()
    {
        return $this->builder->select('id, projectname, description, startdate, enddate');
    }    

    public function getByName($name)
    {
        return $this->builder->where("lower(projectname)", strtolower($name))->get()->getRowArray();
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
