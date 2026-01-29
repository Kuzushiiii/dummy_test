<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\MPurchaseOrder;
use Exception;
use Dompdf\Dompdf;
use Fpdf\Fpdf;


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
            $btn_edit = "<button type='button' class='btn btn-sm btn-warning me-1' onclick=\"window.location.href='" . getURL('purchaseorder/form/' . encrypting($db->id)) . "'\"><i class='bx bx-edit-alt'></i></button>";
            $btn_hapus = '<button type="button" class="btn btn-sm btn-danger" onclick="modalDelete(\'Delete Purchase Order - ' . addslashes($db->transcode) . '\', {\'link\':\'' . getURL('purchaseorder/deleteData') . '\', \'id\':\'' . encrypting($db->id) . '\', \'pagetype\':\'table\'})"><i class=\'bx bx-trash\'></i></button>';

            // tanpa logo (default)
            $btn_print_no_logo = "<button type='button' class='btn btn-sm btn-info me-1' title='Print tanpa logo' onclick=\"window.open('" . getURL('purchaseorder/pdf/' . encrypting($db->id)) . "', '_blank')\"><i class='bx bx-printer'></i></button>";

            // dengan logo (withLogo = 1)
            $btn_print_with_logo = "<button type='button' class='btn btn-sm btn-secondary' title='Print dengan logo' onclick=\"window.open('" . getURL('purchaseorder/pdf/' . encrypting($db->id) . '/1') . "', '_blank')\"><i class='bx bx-image-alt'></i></button>";

            $btn_print = $btn_print_no_logo . $btn_print_with_logo;

            return [
                $no,
                esc($db->transcode),
                esc($db->transdate),
                esc($db->supplydate),
                esc($db->suppliername),
                number_format((float)($db->grandtotal ?? 0), 0, ',', '.'),
                esc($db->description),
                $btn_edit . ' ' . $btn_hapus . ' ' . $btn_print_no_logo . ' ' . $btn_print_with_logo

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

        $suppliers = array_map(function ($s) {
            return ['id' => $s['id'], 'suppliername' => $s['text']];
        }, $this->ModelPoHd->getSuppliers('', 0));

        $details = [];
        if ($id != '') {
            $details = $this->ModelPoHd->getDetail('dt.headerid', $id)
                ->get()
                ->getResultArray();
            $this->ModelPoHd->hitungGrandTotal($id);
            $row = $this->ModelPoHd->getOne($id) ?? [];
        }

        $title = ($form_type == 'edit') ? 'Edit Purchase Order' : 'Add Purchase Order';

        return view('master/document/purchaseorder/v_forms', [
            'title' => $title,
            'breadcrumb' => $this->bc,
            'section' => 'Document',
            'form_type' => $form_type,
            'row' => $row,
            'id' => $id,
            'suppliers' => $suppliers,
            'details' => $details ?? []
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
                'description' => $description,
                'createdby' => getSession('userid'),
                'createddate' => date('Y-m-d H:i:s'),
            ];

            $insertId = $this->ModelPoHd->store($data);

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
                    'csrfToken' => csrf_hash(),
                    'newId' => encrypting((string)$insertId)
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
                'description' => $description,
                'updatedby' => getSession('userid'),
                'updateddate' => date('Y-m-d H:i:s')
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
        $this->db->transBegin();
        try {
            if (empty($id)) throw new Exception(("ID Purchase Order dibutuhkan!"));

            $id = decrypting($id);
            $row = $this->ModelPoHd->getOne($id);

            if (empty($row)) throw new Exception(("Data tidak ditemukan di database."));

            $this->ModelPoHd->destroy('id', $id);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'sukses' => '0',
                    'pesan' => 'Gagal menghapus data transaksi'
                ]);
            } else {
                $this->db->transCommit();
                return $this->response->setJSON([
                    'sukses' => '1',
                    'pesan' => 'Data berhasil dihapus.',
                    'dbError' => db_connect()
                ]);
            }
        } catch (Exception $e) {
            $this->db->transRollback();
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

    public function editDetailModal($Id)
    {
        $detail = $this->ModelPoHd->getDetail('dt.id', $Id)
            ->get()
            ->getRowArray();
        if (!$detail) {
            return $this->response->setStatusCode(404)->setBody('Detail not found');
        }

        $products = array_map(function ($p) {
            return ['id' => $p['id'], 'text' => $p['text']];
        }, $this->ModelPoHd->getProducts('', 0));

        $uoms = array_map(function ($u) {
            return ['id' => $u['id'], 'text' => $u['text']];
        }, $this->ModelPoHd->getUoms('', 0));

        $data = [
            'detail' => $detail,
            'products' => $products,
            'uoms' => $uoms
        ];

        return view('master/document/purchaseorder/v_edit_detail_modal', $data);
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
                'createdby' => getSession('userid'),
                'createddate' => date('Y-m-d H:i:s'),
            ];

            log_message('debug', 'Detail data: ' . json_encode($data));

            if ($detailId > 0) {
                $data['updateddate'] = date('Y-m-d H:i:s');
                $data['updatedby'] = getSession('userid');
                $this->ModelPoHd->updateDetail($data, $detailId);
                $message = 'Detail updated';
                log_message('debug', 'Updated detail ID: ' . $detailId);
            } else {
                $data['isactive'] = true;
                $data['createdby'] = getSession('userid');
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
                'updatedby' => getSession('userid'),
                'updateddate' => date('Y-m-d H:i:s')
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

    public function printPdf($id, $withLogo = false)
    {
        $id = decrypting($id);

        $header  = $this->ModelPoHd->getHeaderWithSupplier($id);
        $details = $this->ModelPoHd
            ->getDetail('dt.headerid', $id)
            ->get()
            ->getResultArray();

        $logo = FCPATH . 'images/logo_hyperdata.jpg';
        $logottd = FCPATH . 'images/tanda_tangan.jpg';

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 9);

        /* ================= HEADER TABLE ================= */

        $headerHeight = 24;
        
        $xStart = $pdf->GetX();
        $yStart = $pdf->GetY();

        $pdf->Cell(30, $headerHeight, '', 1, 0, 'C');

        if ($withLogo && file_exists($logo)) {
            $pdf->Image($logo, $xStart + 6, $yStart + 2, 18);
        }

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(50, $headerHeight, 'PURCHASE ORDER', 1, 0, 'C');

        $xRight = $pdf->GetX();
        $yRight = $pdf->GetY();

        $labelW = 25;
        $valueW = 43;
        $ttdW   = 42;
        $rowH   = 6;

        $pdf->SetFont('Arial', '', 8);

        /* ===== BARIS 1 (top) ===== */
        $pdf->SetXY($xRight, $yRight);
        $pdf->Cell($labelW, $rowH, 'Transaction Code', 1, 0);
        $pdf->Cell($valueW, $rowH, $header['transcode'], 1, 0);
        $pdf->Cell($ttdW, $rowH, '', 'LTRB', 0);

        $pdf->SetXY($xRight + $labelW + $valueW + 1, $yRight + 1);
        $pdf->MultiCell($ttdW - 2, 2.2, "Disetujui Oleh :\nManager", 0, 'C');

        /* ===== BARIS 2 ===== */
        $pdf->SetXY($xRight, $yRight + $rowH);
        $pdf->Cell($labelW, $rowH, 'Supplier', 1, 0);
        $pdf->Cell($valueW, $rowH, $header['suppliername'], 1, 0);
        $pdf->Cell($ttdW, $rowH, '', 'LR', 0);

        /* ===== BARIS 3 ===== */
        $pdf->SetXY($xRight, $yRight + ($rowH * 2));
        $pdf->Cell($labelW, $rowH, 'Tanggal PO', 1, 0);
        $pdf->Cell($valueW, $rowH, date('d F Y', strtotime($header['transdate'])), 1, 0);
        $pdf->Cell($ttdW, $rowH, '', 'LR', 0);

        /* ===== BARIS 4 (bottom) ===== */
        $pdf->SetXY($xRight, $yRight + ($rowH * 3));
        $pdf->Cell($labelW, $rowH, 'Tanggal Supply', 1, 0);
        $pdf->Cell($valueW, $rowH, date('d F Y', strtotime($header['supplydate'])), 1, 0);
        $pdf->Cell($ttdW, $rowH, 'M Tiansyah', 1, 0, 'C');

        if ($withLogo && file_exists($logottd)) {
            $xTtd = $xRight + $labelW + $valueW + 13;
            $yTtd = $yRight + ($rowH) + 1;
            $pdf->Image($logottd, $xTtd, $yTtd, $ttdW - 25);
        }
        
        $pdf->Ln(10);

        /* ================= TABLE HEADER ================= */
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(190, 8, 'Data Penjualan', 0, 1);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 8, 'No', 1, 0, 'C');
        $pdf->Cell(65, 8, 'Product Name', 1, 0, 'C');
        $pdf->Cell(20, 8, 'UOM', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Qty', 1, 0, 'C');
        $pdf->Cell(35, 8, 'Price', 1, 0, 'C');
        $pdf->Cell(40, 8, 'Total', 1, 1, 'C');

        /* ================= TABLE BODY ================= */

        $pdf->SetFont('Arial', '', 10);
        $no = 1;
        $subtotalAll = 0;

        foreach ($details as $d) {
            $subtotal = $d['qty'] * $d['price'];
            $subtotalAll += $subtotal;

            $pdf->Cell(10, 8, $no++, 1, 0, 'C');
            $pdf->Cell(65, 8, $d['productname'], 1);
            $pdf->Cell(20, 8, $d['uomnm'], 1, 0, 'C');
            $pdf->Cell(20, 8, number_format($d['qty'], 0, ',', '.'), 1, 0, 'C');
            $pdf->Cell(35, 8, 'Rp ' . number_format($d['price'], 2, ',', '.'), 1, 0, 'R');
            $pdf->Cell(40, 8, 'Rp ' . number_format($subtotal, 2, ',', '.'), 1, 1, 'R');
        }

        /* ================= SUMMARY ================= */
        $ppn = $subtotalAll * 0.11;
        $grandTotal = $subtotalAll + $ppn;

        $pdf->Ln(4);
        $pdf->SetX(131);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 8, 'Sub Total', 0, 0);
        $pdf->Cell(35, 8, 'Rp ' . number_format($subtotalAll, 3, ',', '.'), 0, 1, 'R');

        $pdf->SetX(131);
        $pdf->Cell(35, 8, 'Discount', 0, 0);
        $pdf->Cell(35, 8, '0', 0, 1, 'R');

        $pdf->SetX(131);
        $pdf->Line(131, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(2);

        $pdf->SetX(131);
        $pdf->Cell(35, 8, 'PPN (11%)', 0, 0);
        $pdf->Cell(35, 8, 'Rp ' . number_format($ppn, 3, ',', '.'), 0, 1, 'R');

        $pdf->SetX(131);
        $pdf->Line(131, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(2);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetX(131);
        $pdf->Cell(35, 8, 'Grand Total', 0, 0);
        $pdf->Cell(35, 8, 'Rp ' . number_format($grandTotal, 3, ',', '.'), 0, 1, 'R');

        $pdf->Output('I', 'Purchase_Order.pdf');
        exit;
    }
}
