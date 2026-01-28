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
            "poh.transdate::text",
            "poh.supplydate::text",
            "mssupplier.suppliername",
            "poh.grandtotal::text",
            "poh.description",
            null,
            null,
        ];
    }

    //menyusun query utama untuk list PO 
    public function datatable()
    {
        $builder = $this->db->table($this->table . ' as poh');

        $builder->select('poh.id, poh.transcode, poh.transdate, poh.supplydate, poh.description, mssupplier.suppliername, 
                      COALESCE(SUM(CASE WHEN dt.isactive = true THEN dt.qty * dt.price ELSE 0 END), 0) as grandtotal')
            ->join('mssupplier', 'mssupplier.id = poh.supplierid', 'left')
            ->join('trpurchaseorderdt dt', 'dt.headerid = poh.id', 'left')
            ->groupBy('poh.id, poh.transcode, poh.transdate, poh.supplydate, poh.description, mssupplier.suppliername')
            ->orderBy('poh.transcode', 'ASC');

        return $builder;
    }

    //mengambil 1 data header
    public function getOne($id)
    {
        return  $this->where("id", $id)->first();
    }

    //insert data header    
    public function store($data)
    {
        return $this->db->table($this->table)->insert($data);    
    }

    //update data header
    public function edit($data, $id)
    {
        return $this->db->table($this->table)->update($data, ['id' => $id]);
    }

    //hapus data header
    public function destroy($column, $value)
    {
        return $this->db->table($this->table)->delete([$column => $value]);
    }

    //kumpulan fungsi untuk bagian purchaseorder detail
    public function getDetail($column, $value)
    {
        return $this->db->table('trpurchaseorderdt as dt')
            ->select('dt.*, p.productname, u.uomnm')
            ->join('msproduct p', 'p.id = dt.productid', 'left')
            ->join('msuom u', 'u.id = dt.uomid', 'left')
            ->where($column, $value)
            ->where('dt.isactive', true);
    }

    // untuk cari headerid dari detail &  untuk redirect balik ke header
    public function getDetailHeaderId($detailId)
    {
        return $this->db->table('trpurchaseorderdt')
            ->select('headerid')
            ->where('id', $detailId)
            ->get()
            ->getRowArray()['headerid'] ?? null;
    }

    //untuk cek apakah data transcode sudah ada
    public function isTransCodeExists($transCode)
    {
        return $this->where('transcode', $transCode)->first() !== null;
    }

    //server side datatable untuk table detail
    public function getDetailsAjaxData($headerId, $search = '', $start = 0, $length = 10)
    {
        $baseBuilder = $this->db->table('trpurchaseorderdt as dt')
            ->select('dt.*, p.productname, u.uomnm')
            ->join('msproduct p', 'p.id = dt.productid', 'left')
            ->join('msuom u', 'u.id = dt.uomid', 'left')
            ->where('dt.headerid', $headerId)
            ->where('dt.isactive', true);

        $recordsTotal = (clone $baseBuilder)->countAllResults();

        $filteredBuilder = clone $baseBuilder;

        if (!empty($search)) {
            $s = strtolower($search);

            // case-insensitive search dengan LOWER(column) dan LIKE
            $filteredBuilder->groupStart()
                ->like('LOWER(p.productname)', $s, 'both', false, true)
                ->orLike('LOWER(u.uomnm)', $s, 'both', false, true)
                ->groupEnd();
        }

        // total setelah filter search
        $recordsFiltered = (clone $filteredBuilder)->countAllResults();

        $data = $filteredBuilder
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        $mappedData = array_map(function ($row) {
            return [
                esc($row['productname']),
                esc($row['uomnm']),
                esc(rtrim(rtrim(number_format($row['qty'], 0, ',', '.'), '0'), '.')),
                number_format($row['price'], 0, ',', '.'),
                number_format($row['qty'] * $row['price'], 0, ',', '.'),
                '<button class="btn btn-sm btn-warning" onclick="editDetail(' . $row['id'] . ', \'' . $row['productid'] . '\', \'' . $row['uomid'] . '\', \'' . $row['qty'] . '\', \'' . $row['price'] . '\', \'' . addslashes($row['productname']) . '\', \'' . addslashes($row['uomnm']) . '\')"><i class="bx bx-edit-alt"></i></button> ' .
                '<button class="btn btn-sm btn-danger" onclick="modalDelete(\'Hapus Detail Purchase Order - ' . addslashes($row['productname']) . '\', {\'link\':\'' . getURL('purchaseorder/deleteDetail') . '\', \'id\':\'' . $row['id'] . '\', \'pagetype\':\'table\', \'table-id\':\'detailsTable\'})"><i class="bx bx-trash"></i></button>'
            ];
        }, $data);

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $mappedData
        ];
    }

    //insert data detail
    public function addDetail($data)
    {
        return $this->db->table('trpurchaseorderdt')->insert($data);
    }

    public function updateDetail($data, $id)
    {
        return $this->db->table('trpurchaseorderdt')->update($data, ['id' => $id]);
    }

    //menghapus data detaul (soft delete)
    public function deleteDetail($id) 
    {
        return $this->db->table('trpurchaseorderdt')->update(['isactive' => false], ['id' => $id]);
    }

    //ambil data total 
    public function getGrandTotal($id)
    {
        return $this->db->table('trpurchaseorderdt')
            ->selectSum('(qty * price)', 'grandtotal')
            ->where('headerid', $id)
            ->where('isactive', true)
            ->get()
            ->getRowArray()['grandtotal'] ?? 0;
    }

    //hitung ulang total dari detail
    public function hitungGrandTotal($id)
    {
        $total = $this->getGrandTotal($id);

        $this->db->table('trpurchaseorderhd')
            ->update(['grandtotal' => $total], ['id' => $id]);

        return $total;
    }

    //ambil data supplier berdasarkan id
    public function getSupplierById($id)
    {
        return $this->db->table('mssupplier')
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    //ambil data suppliers untuk select2
    public function getSuppliers($search = '', $limit = 10)
    {
        $builder = $this->db->table('mssupplier')
            ->select('id, suppliername as text');
        if ($search) {
            $builder->like('LOWER(suppliername)', strtolower($search));
        }
        $query = $builder->orderBy('suppliername', 'ASC');
        if ($limit > 0) {
            $query->limit($limit);
        }
        return $query->get()->getResultArray();
    }

    //ambil data products untuk select2
    public function getProducts($search = '', $limit = 10)
    {
        $builder = $this->db->table('msproduct')
            ->select('id, productname as text, price');
        if ($search) {
            $builder->like('LOWER(productname)', strtolower($search));
        }
        $query = $builder->orderBy('productname', 'ASC');
        if ($limit > 0) {
            $query->limit($limit);
        }
        return $query->get()->getResultArray();
    }

    //ambil data uom untuk select2
    public function getUoms($search = '', $limit = 10)
    {
        $builder = $this->db->table('msuom')
            ->select('id, uomnm as text')
            ->where('isactive', true);
        if ($search) {
            $builder->like('LOWER(uomnm)', strtolower($search));
        }
        $query = $builder->orderBy('uomnm', 'ASC');
        if ($limit > 0) {
            $query->limit($limit);
        }
        return $query->get()->getResultArray();
    }

    //untuk ambil data header dan suppliername (untuk export to pdf)
    public function getSupplier($id)
    {
        return $this->db->table('trpurchaseorderhd as poh')
            ->select('poh.*, mssupplier.suppliername')
            ->join('mssupplier', 'mssupplier.id = poh.supplierid', 'left')
            ->where('poh.id', $id)
            ->get()
            ->getRowArray();
    }

}

?>