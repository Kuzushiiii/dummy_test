<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\MPurchaseOrder;
use Exception;

class PurchaseOrder extends BaseController
{
    protected $ModelPoHd;
    protected $bc;
    protected $db;
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger

    ) {
        parent::initController($request, $response, $logger);

        $this->db = \Config\Database::connect();

        $this->ModelPoHd = new MPurchaseOrder();

        $this->bc = [
            [
                'Document',
                'Purchase Order'
            ]
        ];
    }

    public function index()
    {
        return view('master/document/purchaseorder/v_list', [
            'title'   => 'Purchase Order',
            'breadcrumb' => $this->bc,
            'section' => 'Document'
        ]);
    }

    public function viewLogin()
    {
        return view('login/v_login', [
            'title' => 'Login'
        ]);
    }

    public function datatable()
    {
        $table = Datatables::method([MPurchaseOrder::class, 'datatable'], 'searchable')->make();

        $table->updateRow(function ($db, $no) {
            $btn_edit = "<button type='button' class='btn btn-sm btn-warning' onclick=\"modalForm('Edit Purchase Order - " . $db->transcode . "', 'modal-lg', '" . getURL('purchaseorder/form/' . encrypting($db->id)) . "', {identifier: this})\"><i class='bx bx-edit-alt'></i></button>";
            $btn_hapus = '<button type="button" class="btn btn-sm btn-danger" onclick="modalDelete(\'Delete Purchase Order - ' . addslashes($db->transcode) . '\', {\'link\':\'' . getURL('purchaseorder/deleteData') . '\', \'id\':\'' . encrypting($db->id) . '\', \'pagetype\':\'table\'})"><i class=\'bx bx-trash\'></i></button>';
            return [
                $no,
                esc($db->transcode),
                esc($db->transdate),
                esc($db->supplydate),
                esc($db->suppliername),
                number_format($db->grandtotal ?? 0, 2, ',', '.'),
                esc($db->description),
                $btn_edit . ' ' . $btn_hapus
            ];
        });
        $table->toJson();
    }

    public function forms($id = '')
    {
        $form_type = (empty($id)) ? 'add' : 'edit';
        $row = [];

        if ($id != '') {
            $id = decrypting($id);
            $row = $this->ModelPoHd->getOne($id) ?? [];

            if ($form_type == 'edit' && !empty($row)) {
                $supplier = $this->ModelPoHd->getSupplierById($row['supplierid']);
                $row['suppliername'] = $supplier['suppliername'] ?? '';
            }
        }

        $suppliers = array_map(function($s) {
            return ['id' => $s['id'], 'suppliername' => $s['text']];
        }, $this->ModelPoHd->getSuppliers('', 0));

        $details = [];
        if ($id != '') {
            $details = $this->ModelPoHd->getDetails($id);
            $this->ModelPoHd->hitungGrandTotal($id);
            $row = $this->ModelPoHd->getOne($id) ?? [];
        }

        $dt['view'] = view('master/document/purchaseorder/v_form', [
            'form_type' => $form_type,
            'row' => $row,
            'id' => $id,
            'suppliers' => $suppliers,
            'details' => $details
        ]);

        $dt['csrfToken'] = csrf_hash();
        echo json_encode($dt);
    }

    public function addData()
    {
        log_message('debug', 'addData called with transCode: ' . $this->request->getPost('transactionCode'));

        $transactionCode = $this->request->getPost('transactionCode');
        $transactionDate = $this->request->getPost('transactionDate');
        $supplierId = $this->request->getPost('supplierId');
        $supplyDate = $this->request->getPost('supplyDate');
        $description = $this->request->getPost('description');

        $this->db->transBegin();
        try {
            if (empty($transactionCode)) throw new Exception(("Kode Transaksi dibutuhkan!"));
            if (empty($transactionDate)) throw new Exception(("Tanggal Transaksi dibutuhkan!"));
            if (empty($supplierId)) throw new Exception(("Supplier dibutuhkan!"));
            if ($this->ModelPoHd->isTransCodeExists($transactionCode)) {
                throw new Exception('Kode Transaksi sudah ada!');
            }

            $data = [
                'transcode' => $transactionCode,
                'transdate' => $transactionDate,
                'supplierid' => $supplierId,
                'supplydate' => $supplyDate,
                'grandtotal' => 0.0,
                'description' => $description
            ];

            $insertId = $this->ModelPoHd->insert($data);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'sukses' => '0',
                    'pesan' => 'Gagal menyimpan data transaksi'
                ]);
            } else {
                $this->db->transCommit();
                return $this->response->setJSON([
                    'sukses' => '1',
                    'pesan' => 'Data berhasil disimpan.',
                    'csrfToken' => csrf_hash()
                ]);
            }
        } catch (Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON(['sukses' => '0', 'pesan' => $e->getMessage()]);
        }
    }

    public function updateData()
    {
        $purchaseOrderId = decrypting($this->request->getPost('purchaseOrderId'));
        $transactionCode = $this->request->getPost('transactionCode');
        $transactionDate = $this->request->getPost('transactionDate');
        $supplierId = $this->request->getPost('supplierId');
        $supplyDate = $this->request->getPost('supplyDate');
        $description = $this->request->getPost('description');

        // Calculate grand total from current details
        $grandTotal = $this->ModelPoHd->getGrandTotal($purchaseOrderId);

        $this->db->transBegin();
        try {
            if (empty($purchaseOrderId)) throw new Exception(("ID Purchase Order dibutuhkan!"));
            if (empty($transactionCode)) throw new Exception(("Kode Transaksi dibutuhkan!"));
            if (empty($transactionDate)) throw new Exception(("Tanggal Transaksi dibutuhkan!"));
            if (empty($supplierId)) throw new Exception(("Supplier dibutuhkan!"));

            $data = [
                'transcode' => $transactionCode,
                'transdate' => $transactionDate,
                'supplierid' => $supplierId,
                'supplydate' => $supplyDate,
                'grandtotal' => $grandTotal,
                'description' => $description
            ];

            $this->ModelPoHd->edit($data, $purchaseOrderId);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'sukses' => '0',
                    'pesan' => 'Gagal mengupdate data transaksi'
                ]);
            } else {
                $this->db->transCommit();
                return $this->response->setJSON([
                    'sukses' => '1',
                    'pesan' => 'Data berhasil diupdate.',
                    'csrfToken' => csrf_hash()
                ]);
            }
        } catch (Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON(['sukses' => '0', 'pesan' => $e->getMessage()]);
        }
    }

    public function deleteData()
    {
        $id = $this->request->getPost('id');
        $res = array();
        $this->ModelPoHd->transBegin();
        try {
            if (empty($id)) throw new Exception(("ID Purchase Order dibutuhkan!"));

            $id = decrypting($id);
            $row = $this->ModelPoHd->getOne($id);

            if (empty($row)) throw new Exception(("Data tidak ditemukan di database."));

            $this->ModelPoHd->destroy('id', $id);

            if ($this->ModelPoHd->transStatus() === false) {
                $this->ModelPoHd->transRollback();
                return $this->response->setJSON([
                    'sukses' => '0',
                    'pesan' => 'Gagal menghapus data transaksi'
                ]);
            } else {
                $this->ModelPoHd->transCommit();
                return $this->response->setJSON([
                    'sukses' => '1',
                    'pesan' => 'Data berhasil dihapus.',
                    'dbError' => db_connect()
                ]);
            }
        } catch (Exception $e) {
            $this->ModelPoHd->transRollback();
            return $this->response->setJSON([
                'sukses' => '0',
                'pesan' => $e->getMessage(),
                'traceString' => $e->getTraceAsString(),
                'dbError' => db_connect()->error()
            ]);
        }
    }

    public function getSuppliers()
    {
        $search = $this->request->getPost('searchTerm') ?? '';
        $suppliers = $this->ModelPoHd->getSuppliers($search, 10);
        return $this->response->setJSON([
            'data' => $suppliers,
            'csrfToken' => csrf_hash()
        ]);
    }

    public function getProducts()
    {
        $search = $this->request->getPost('searchTerm') ?? '';
        $products = $this->ModelPoHd->getProducts($search, 10);

        return $this->response->setJSON([
            'data' => $products,
            'csrfToken' => csrf_hash()
        ]);
    }

    public function getUoms()
    {
        $search = $this->request->getPost('searchTerm') ?? '';
        $uoms = $this->ModelPoHd->getUoms($search, 10);

        return $this->response->setJSON([
            'data' => $uoms,
            'csrfToken' => csrf_hash()
        ]);
    }

    public function getDetailsAjax()
    {
        $headerId = decrypting($this->request->getPost('headerId'));
        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start') ?? 0;
        $length = $this->request->getPost('length') ?? 10;
        $search = $this->request->getPost('search')['value'] ?? '';

        $result = $this->ModelPoHd->getDetailsAjaxData($headerId, $search, $start, $length);

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data' => $result['data']
        ]);
    }

    public function addDetail()
    {
        log_message('debug', 'addDetail called with productId: ' . $this->request->getPost('productId'));
        log_message('debug', 'addDetail called with post data: ' . json_encode($this->request->getPost()));
        $headerEncypted = $this->request->getPost('headerId');
        $headerId = decrypting($headerEncypted);
        $productId = $this->request->getPost('productId');
        $uomId = $this->request->getPost('uomId');
        $qty = (float) $this->request->getPost('qty');
        $price = (float) $this->request->getPost('price');
        $detailId = (int) $this->request->getPost('id');

        $this->db->transBegin();

        try {
            if ($qty <= 0) {
                throw new \Exception('Qty harus lebih dari 0');
            }

            $price = (float) $this->request->getPost('price');

            $data = [
                'headerid' => $headerId,
                'productid' => $productId,
                'uomid' => $uomId,
                'qty' => $qty,
                'price' => $price,
            ];

            log_message('debug', 'Detail data: ' . json_encode($data));

            if ($detailId > 0) {
                $data['updateddate'] = date('Y-m-d H:i:s');
                $data['updatedby'] = session('userid');
                $this->ModelPoHd->updateDetail($data, $detailId);
                $message = 'Detail updated';
                log_message('debug', 'Updated detail ID: ' . $detailId);
            } else {
                $data['isactive'] = true;
                $data['createdby'] = session('userid');
                $data['createddate'] = date('Y-m-d H:i:s');
                $this->ModelPoHd->addDetail($data);
                $message = 'Detail added';
            }

            $this->ModelPoHd->hitungGrandTotal($headerId);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'sukses' => 0,
                    'pesan' => 'Gagal menyimpan detail transaksi'
                ]);
            } else {
                $this->db->transCommit();
                log_message('debug', 'addDetail success, message: ' . $message);
                return $this->response->setJSON([
                    'sukses' => 1,
                    'pesan' => $message,
                    'csrfToken' => csrf_hash(),
                    'grandtotal' => $this->ModelPoHd->getGrandTotal($headerId)
                ]);
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'updateDetail error: ' . $e->getMessage());
            return $this->response->setJSON(['sukses' => 0, 'pesan' => $e->getMessage()]);
        }
    }

    public function updateDetail()
    {
        log_message('debug', 'updateDetail called with post data: ' . json_encode($this->request->getPost()));
        $headerEncypted = $this->request->getPost('headerId');
        $headerId = decrypting($headerEncypted);
        $detailId = (int) $this->request->getPost('id');
        $productId = $this->request->getPost('productId');
        $uomId = $this->request->getPost('uomId');
        $qty = (float) $this->request->getPost('qty');
        $price = (float) $this->request->getPost('price');

        if ($detailId <= 0) {
            return $this->response->setJSON(['sukses' => 0, 'pesan' => 'Detail ID required for update']);
        }

        $this->db->transBegin();

        try {
            if ($qty <= 0) {
                throw new \Exception('Qty harus lebih dari 0');
            }

            $data = [
                'productid' => $productId,
                'uomid' => $uomId,
                'qty' => $qty,
                'price' => $price,
                'updateddate' => date('Y-m-d H:i:s'),
                'updatedby' => session('userid')
            ];

            $this->ModelPoHd->updateDetail($data, $detailId);
            $this->ModelPoHd->hitungGrandTotal($headerId);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'sukses' => 0,
                    'pesan' => 'Gagal mengupdate detail transaksi'
                ]);
            } else {
                $this->db->transCommit();
                log_message('debug', 'updateDetail success for id: ' . $detailId);
                 return $this->response->setJSON([
                     'sukses' => 1,
                     'pesan' => 'Detail updated',
                     'csrfToken' => csrf_hash(),
                     'grandtotal' => $this->ModelPoHd->getGrandTotal($headerId)
                 ]);
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();

            return $this->response->setJSON([
                'sukses' => 0,
                'pesan' => $e->getMessage()
            ]);
        }
    }

    public function deleteDetail()
    {
        log_message('debug', 'deleteDetail called with: ' . json_encode($this->request->getPost()));

        $this->db->transBegin();

        try {
            $detailId = $this->request->getPost('id');
            log_message('debug', 'detailId: ' . $detailId);
            if (empty($detailId)) throw new Exception('Detail ID required');

            $headerId = $this->ModelPoHd->getDetailHeaderId($detailId);
            if (!$headerId) throw new Exception('Detail not found');
            log_message('debug', 'headerId: ' . $headerId);
            $this->ModelPoHd->deleteDetail($detailId);
            $this->ModelPoHd->hitungGrandTotal($headerId);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'sukses' => 0,
                    'pesan' => 'Gagal menghapus detail transaksi'
                ]);
            } else {
                $this->db->transCommit();
                return $this->response->setJSON([
                    'sukses' => 1,
                    'pesan' => 'Detail deleted',
                    'csrfToken' => csrf_hash(),
                    'grandtotal' => $this->ModelPoHd->getGrandTotal($headerId)
                ]);
            }
        } catch (Exception $e) {
            $this->db->transRollback();
            log_message('error', 'deleteDetail error: ' . $e->getMessage());
            return $this->response->setJSON(['sukses' => 0, 'pesan' => $e->getMessage()]);
        }
    }
}
