<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\MSupplier;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Fpdf\Fpdf;

class Supplier extends BaseController
{
    protected $db;
    protected $bc;
    protected $MSupplier;

    public function __construct()
    {
        $this->bc = [
            [
                'Setting',
                'Supplier'
            ]
        ];

        $this->MSupplier = new MSupplier();
    }

    public function index()
    {
        return view('master/supplier/v_supplier', [
            'title' => 'Supplier',
            'akses' => null,
            'breadcrumb' => $this->bc,
            'section' => 'Setting Supplier',
        ]);
    }

    public function forms($id = '')
    {
        $form_type = (empty($id) ? 'add' : 'edit');
        $row = array();
        if ($id != '') {
            $id = decrypting($id);
            $row = $this->MSupplier->find($id);
        }
        $dt['view'] = view('master/supplier/v_form', [
            'form_type' => $form_type,
            'row' => $row,
            'userid' => $id,
            'title' => 'Supplier Form'
        ]);
        $dt['csrfToken'] = csrf_hash();
        echo json_encode($dt);
    }

    public function add()
    {
        $suppliername = $this->request->getPost('suppliername');
        $address = $this->request->getPost('address');
        $phone = $this->request->getPost('phone');
        $email = $this->request->getPost('email');
        $filepath = $this->request->getFile('filepath');
        $res = array();

        $this->db->transBegin();
        try {
            // Validasi input
            if (empty($suppliername))
                throw new Exception('Masukkan nama supplier');
            if (empty($address))
                throw new Exception('Masukkan alamat');
            if (empty($phone))
                throw new Exception('Masukkan nomor HP');
            if (empty($email))
                throw new Exception('Masukkan email');

            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $extension = $filepath->getExtension();
            if (!in_array($extension, $allowedExtensions)) {
                throw new Exception("Format filepath tidak valid, hanya jpg, jpeg, dan png yang diperbolehkan!");
            }

            $filename = $filepath->getRandomName();
            $filepath->move('uploads/supplier/', $filename);
            $fileurl = 'uploads/supplier/' . $filename;

            // Insert data
            $this->MSupplier->store([
                'suppliername' => $suppliername,
                'address' => $address,
                'phone' => $phone,
                'email' => $email,
                'filepath' => $fileurl,
                'createdby' => getSession('userid'),
                'createddate' => date('Y-m-d H:i:s'),
                'updatedby' => getSession('userid'),
                'updateddate' => date('Y-m-d H:i:s')
            ]);

            $res = [
                'status' => '1',
                'message' => 'Supplier added successfully',
                "dbError" => db_connect()
            ];
            $this->db->transCommit();
        } catch (Exception $e) {
            $this->db->transRollback();
            $res = [
                'sukses' => '0',
                'message' => $e->getMessage(),
                'traceString' => $e->getTraceAsString(),
                'dbError' => db_connect()->error()
            ];
        }
        $this->db->transComplete();
        echo json_encode($res);
    }

    public function datatable()
    {
        $table = Datatables::method([MSupplier::class, 'datatable'], 'searchable')
            ->make();

        $table->updateRow(function ($db, $no) {
            $btn_edit = "<button type='button' class='btn btn-sm btn-warning' onclick=\"modalForm('Update User - " . $db->suppliername . "', 'modal-lg', '" . getURL('supplier/form/' . encrypting($db->id)) . "', {identifier: this})\"><i class='bx bx-edit-alt'></i></button>";
            $btn_hapus = "<button type='button' class='btn btn-sm btn-danger' onclick=\"modalDelete('Delete User - " . $db->suppliername . "', {'link':'" . getURL('supplier/delete') . "', 'id':'" . encrypting($db->id) . "', 'pagetype':'table'})\"><i class='bx bx-trash'></i></button>";
            $btn_print = "<button type='button' class='btn btn-sm btn-info' onclick=\"window.open('" . getURL('supplier/pdf/' . encrypting($db->id)) . "', '_blank')\"><i class='bx bx-printer'></i></button>";
            $foto = !empty($db->filepath)
                ? "<div style='display:flex;align-items:center;justify-content:center;'>
         <img src='" . base_url($db->filepath) . "'
              alt='supplier'
              style='width:45px;height:45px;border-radius:50%;object-fit:cover;'>
       </div>"
                : "<div style='text-align:center;'>-</div>";
            return [
                $no,
                $db->suppliername,
                $db->address,
                $db->phone,
                $db->email,
                $foto,
                "<div style='display:flex;align-items:center;justify-content:center;'>$btn_edit&nbsp;$btn_hapus&nbsp;$btn_print</div>"
            ];
        });
        $table->toJson();
    }

    public function update()
    {
        $supplierid = $this->request->getPost('id');
        $suppliername = $this->request->getPost('suppliername');
        $address = $this->request->getPost('address');
        $phone = $this->request->getPost('phone');
        $email = $this->request->getPost('email');
        $filepath = $this->request->getFile('filepath');
        $res = array();

        $this->db->transBegin();
        try {
            // Validasi input
            if (empty($suppliername))
                throw new Exception('Masukkan nama supplier');
            if (empty($address))
                throw new Exception('Masukkan alamat');
            if (empty($phone))
                throw new Exception('Masukkan nomor HP');
            if (empty($email))
                throw new Exception('Masukkan email');

            $data = [
                'suppliername' => $suppliername,
                'address' => $address,
                'phone' => $phone,
                'email' => $email,
                'updatedby' => getSession('userid'),
                'updateddate' => date('Y-m-d H:i:s')
            ];

            if ($filepath && $filepath->isValid()) {
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $extension = $filepath->getExtension();
                if (!in_array($extension, $allowedExtensions)) {
                    throw new Exception("Format foto tidak valid, hanya jpg, jpeg, dan png yang diperbolehkan!");
                }
                $oldFilePath = $this->MSupplier->getOne($supplierid)['filepath'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
                $newName = $filepath->getRandomName();
                $filepath->move('uploads/supplier/', $newName);
                $data['filepath'] = 'uploads/supplier/' . $newName;
            }

            $this->MSupplier->edit($data, $supplierid);
            $res = [
                'status' => '1',
                'message' => 'Supplier updated successfully',
                'csrf_token' => csrf_hash(),
                'dbError' => db_connect()
            ];
            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'status' => '0',
                'message' => $e->getMessage(),
                'csrf_token' => csrf_hash(),
                'dbError' => db_connect()->error()
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        echo json_encode($res);
    }

    public function delete()
    {
        $userid = decrypting($this->request->getPost('id'));
        $res = array();
        $this->db->transBegin();
        try {
            $row = $this->MSupplier->getOne($userid);
            if (empty($row))
                throw new Exception("User not found!");
            $this->MSupplier->destroy('id', $userid);
            $res = [
                'status' => '1',
                'message' => 'Data deleted successfully!',
                'dbError' => db_connect()->error()
            ];
        } catch (Exception $e) {
            $res = [
                'status' => '0',
                'message' => $e->getMessage(),
                'traceString' => $e->getTraceAsString(),
                'dbError' => db_connect()->error()
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        echo json_encode($res);
    }

    public function exportexcel()
    {
        $data = $this->MSupplier->getAll();
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Supplier_Data');

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => '4CAF50'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $headers = ['Supplier Name', 'Address', 'Phone', 'Email', 'File Path'];
        $columns = range('A', 'E');

        foreach ($columns as $key => $column) {
            $sheet->setCellValue($column . '1', $headers[$key]);
        }
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        $i = 2;
        foreach ($data as $row) {
            $sheet->setCellValue('A' . $i, $row['suppliername']);
            $sheet->setCellValue('B' . $i, $row['address']);
            $sheet->setCellValue('C' . $i, $row['phone']);
            $sheet->setCellValue('D' . $i, $row['email']);
            $sheet->setCellValue('E' . $i, $row['filepath']);
            $i++;
        }
        $sheet->getStyle('A2:E' . ($i - 1))->applyFromArray($dataStyle);
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Supplier_Zaevanza_' . date('dmY') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function fpdf($id = null)
    {
        $pdf = new FPDF();
        $data = $id ? $this->MSupplier->find(decrypting($id)) : $this->MSupplier->getAll();

        $pdf->SetTitle('Supplier Data');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);

        // Title
        $pdf->Cell(0, 10, 'Supplier Data Report', 0, 1, 'C');
        $pdf->Ln(10);

        // Header
        $pdf->Cell(10, 10, 'No', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Supplier Name', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Address', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Phone', 1, 0, 'C');
        $pdf->Cell(60, 10, 'Email', 1, 1, 'C'); 

        $pdf->SetFont('Arial', '', 12);
        if ($id) {
            if (is_array($data) || is_object($data)) {
                $pdf->Cell(10, 10, 1, 1, 0, 'C');
                $pdf->Cell(40, 10, $data['suppliername'], 1, 0, 'L');
                $pdf->Cell(40, 10, $data['address'], 1, 0, 'L');
                $pdf->Cell(40, 10, $data['phone'], 1, 0, 'L');
                $pdf->Cell(60, 10, $data['email'], 1, 1, 'L');
            } else {
                // Handle the case where $data is not an array or object
                $pdf->Cell(0, 10, 'No data found', 1, 1, 'C');
            }
        } else {
            $i = 1;
            foreach ($data as $row) {
                $pdf->Cell(10, 10, $i, 1, 0, 'C');
                $pdf->Cell(40, 10, $row['suppliername'], 1, 0, 'L');
                $pdf->Cell(40, 10, $row['address'], 1, 0, 'L');
                $pdf->Cell(40, 10, $row['phone'], 1, 0, 'L');
                $pdf->Cell(60, 10, $row['email'], 1, 1, 'L'); 
                $i++;
            }
        }

        $pdf->Output('D', 'Supplier_Data.pdf');
        exit;
    }
}
