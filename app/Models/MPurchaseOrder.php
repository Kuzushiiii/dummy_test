<?php

namespace App\Models;

use CodeIgniter\Model;

class MPurchaseOrder extends Model
{
    protected $db;
    protected $table = 'trpurchaseorderhd';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'transcode',
        'transdate',
        'supplierid',
        'supplydate',
        'grandtotal',
        'description',
        'createddate',
        'createdby',
        'updateddate',
        'updatedby'
    ];

    public function __construct()
    {
        parent::__construct();
    }


    public function searchable()
    {
        return [
            null,
            "poh.transcode",
            "poh.transdate",
            "poh.supplydate",
            "mssupplier.suppliername",
            "poh.grandtotal",
            "poh.description",
            null,
            null,
        ];
    }

    public function datatable()
    {
        $builder = $this->db->table($this->table . ' as poh');
        $builder->select('poh.*, mssupplier.suppliername')
            ->join('mssupplier', 'mssupplier.id = poh.supplierid', 'left')
            ->orderBy('poh.transcode', 'ASC');

        return $builder;
    }

    public function getOne($id)
    {
        return  $this->where("id", $id)->first();
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

    //kumpulan fungsi untuk bagian purchaseorder detail
    public function getDetails($headerId)
    {
        return $this->db->table('trpurchaseorderdt as dt')
            ->select('dt.*, p.productname, u.uomnm')
            ->join('msproduct p', 'p.id = dt.productid', 'left')
            ->join('msuom u', 'u.id = dt.uomid', 'left')
            ->where('dt.headerid', $headerId)
            ->where('dt.isactive', true)
            ->get()
            ->getResultArray();
    }

    public function getDetailsAjaxData($headerId, $search = '', $start = 0, $length = 10)
    {
        $builder = $this->db->table('trpurchaseorderdt as dt')
            ->select('dt.*, p.productname, u.uomnm')
            ->join('msproduct p', 'p.id = dt.productid', 'left')
            ->join('msuom u', 'u.id = dt.uomid', 'left')
            ->where('dt.headerid', $headerId)
            ->where('dt.isactive', true);

        // Get total records without search
        $recordsTotal = $builder->countAllResults(false);

        // Clone builder for filtered count and data retrieval
        $filteredBuilder = clone $builder;

        // Apply search to filtered builder if present
        if (!empty($search)) {
            $s = $this->db->escapeLikeString($search);
            $filteredBuilder->groupStart()
                ->like("LOWER(p.productname)", $s, 'both', false)
                ->orLike("LOWER(u.uomnm)", $s, 'both', false)
                ->groupEnd();
        }

        // Get filtered count
        $recordsFiltered = $filteredBuilder->countAllResults(false);

        // Apply limit for pagination and get data
        $filteredBuilder->limit($length, $start);
        $data = $filteredBuilder->get()->getResultArray();

        $mappedData = array_map(function ($row) {
            return [
                esc($row['productname']),
                esc($row['uomnm']),
                esc($row['qty']),
                number_format($row['price'], 2, ',', '.'),
                number_format($row['qty'] * $row['price'], 2, ',', '.'),
                '<button class="btn btn-sm btn-warning" onclick="editDetail(' . $row['id'] . ', \'' . $row['productid'] . '\', \'' . $row['uomid'] . '\', \'' . $row['qty'] . '\', \'' . $row['price'] . '\', \'' . addslashes($row['productname']) . '\')"><i class="bx bx-edit-alt"></i></button> ' .
                '<button class="btn btn-sm btn-danger" onclick="modalDelete(\'Hapus Detail Purchase Order - ' . addslashes($row['productname']) . '\', {\'link\':\'' . getURL('purchaseorder/deleteDetail') . '\', \'id\':\'' . $row['id'] . '\', \'pagetype\':\'table\', \'table-id\':\'detailsTable\'})"><i class="bx bx-trash"></i></button>'
            ];
        }, $data);

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $mappedData
        ];
    }

    public function addDetail($data)
    {
        return $this->db->table('trpurchaseorderdt')->insert($data);
    }

    public function updateDetail($data, $id)
    {
        return $this->db->table('trpurchaseorderdt')->update($data, ['id' => $id]);
    }

    public function deleteDetail($id) 
    {
        return $this->db->table('trpurchaseorderdt')->update(['isactive' => false], ['id' => $id]);
    }

    public function hitungGrandTotal($id)
    {
        $total = $this->db->table('trpurchaseorderdt')
            ->selectSum('(qty * price)', 'grandtotal')
            ->where('headerid', $id)
            ->where('isactive', true)
            ->get()
            ->getRowArray()['grandtotal'] ?? 0;

        $this->db->table('trpurchaseorderhd')
            ->where('id', $id)
            ->update(['grandtotal' => $total]);
        return $total;
    }

    public function getGrandTotal($id)
    {
        return $this->db->table('trpurchaseorderdt')
            ->selectSum('(qty * price)', 'grandtotal')
            ->where('headerid', $id)
            ->where('isactive', true)
            ->get()
            ->getRowArray()['grandtotal'] ?? 0;
    }

    public function getSupplierById($id)
    {
        return $this->db->table('mssupplier')
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    public function getSuppliers($search = '', $limit = 10)
    {
        $builder = $this->db->table('mssupplier')
            ->select('id, suppliername as text');
        if ($search) {
            $builder->like('suppliername', $search);
        }
        $query = $builder->orderBy('suppliername', 'ASC');
        if ($limit > 0) {
            $query->limit($limit);
        }
        return $query->get()->getResultArray();
    }

    public function getProducts($search = '', $limit = 10)
    {
        $builder = $this->db->table('msproduct')
            ->select('id, productname as text, price');
        if ($search) {
            $builder->like('productname', $search);
        }
        $query = $builder->orderBy('productname', 'ASC');
        if ($limit > 0) {
            $query->limit($limit);
        }
        return $query->get()->getResultArray();
    }

    public function getUoms($search = '', $limit = 10)
    {
        $builder = $this->db->table('msuom')
            ->select('id, uomnm as text')
            ->where('isactive', true);
        if ($search) {
            $builder->like('uomnm', $search);
        }
        $query = $builder->orderBy('uomnm', 'ASC');
        if ($limit > 0) {
            $query->limit($limit);
        }
        return $query->get()->getResultArray();
    }

}

?>