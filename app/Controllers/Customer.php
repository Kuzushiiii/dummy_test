<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\MCustomer;
use App\Models\MUser;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Fpdf\Fpdf;

class Customer extends BaseController
{
    protected $customerModel;
    protected $bc;
    protected $db;
    public function __construct()
    {
        $this->customerModel = new MCustomer();
        $this->bc = [
            [
                'Setting',
                'Customer'
            ]
        ];
    }

    public function index()
    {
        return view('master/customer/v_customer', [
            'title' => 'Customer',
            'akses' => null,
            'breadcrumb' => $this->bc,
            'section' => 'Setting User',
        ]);
    }

    public function viewLogin()
    {
        return view('login/v_login', [
            'title' => 'Login'
        ]);
    }

    public function loginAuth()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $res = array();
        $this->db->transBegin();
        try {
            if (empty($username) || empty($password)) throw new Exception("Username atau Password harus diisi!");
            $row = $this->customerModel->getByName($username);
            if (empty($row)) throw new Exception("User tidak terdaftar di sistem!");
            if (password_verify($password, $row['password'])) {
                setSession('userid', $row['id']);
                setSession('name', $row['fullname']);
                $res = [
                    'sukses' => '1',
                    'pesan' => 'Berhasil Login',
                    'link' => base_url('user'),
                    'dbError' => db_connect()->error()
                ];
            } else {
                throw new Exception("Password user salah, coba lagi!");
            }
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => $e->getMessage(),
                'traceString' => $e->getTraceAsString(),
                'dbError' => db_connect()->error()
            ];
        }
        $this->db->transComplete();
        echo json_encode($res);
    }

    public function datatable()
    {
        $table = Datatables::method([MCustomer::class, 'datatable'], 'searchable')->make();

        $table->updateRow(function ($db, $no) {
            $btn_edit = "<button type='button' class='btn btn-sm btn-warning' onclick=\"modalForm('Update Customer - " . $db->customername . "', 'modal-lg', '" . getURL('customer/form/' . ($db->id)) . "', {identifier: this})\"><i class='bx bx-edit-alt'></i></button>";
            $btn_hapus = "<button type='button' class='btn btn-sm btn-danger' onclick=\"modalDelete('Delete Customer - " . $db->customername . "', {'link':'" . getURL('customer/delete') . "', 'id':'" . encrypting($db->id) . "', 'pagetype':'table'})\"><i class='bx bx-trash'></i></button>";

            $foto_profil = !empty($db->filepath)
                ? "<img src='" . htmlspecialchars($db->filepath) . "' alt='Foto Profil' width='50' height='50' style='border-radius: 50%; object-fit: cover;'>"
                : "<img src='path/to/default-image.png' alt='Foto Profil Default' width='50' height='50' style='border-radius: 50%; object-fit: cover;'>";

            return [
                $no,
                $foto_profil,
                $db->customername,
                $db->address,
                $db->phone,
                $db->email,
                "<div style='display:flex;align-items:center;justify-content:center;'>$btn_edit&nbsp;$btn_hapus</div>"
            ];
        });
        $table->toJson();
    }

    public function forms($customerid = '')
    {
        $form_type = (empty($customerid) ? 'add' : 'edit');
        $row = [];
        if ($customerid != '') {
            $customerid = decrypting($customerid);
            $row = $this->customerModel->getOne($customerid);
        }
        $dt['view'] = view('master/customer/v_form', [
            'form_type' => $form_type,
            'row' => $row,
            'id' => $customerid
        ]);
        $dt['csrfToken'] = csrf_hash();
        echo json_encode($dt);
    }

    public function addData()
    {
        $foto = $this->request->getFile('foto'); // Ambil file foto
        $nama = $this->request->getPost('nama');
        $alamat = $this->request->getPost('alamat');
        $telepon = $this->request->getPost('telepon');
        $email = $this->request->getPost('email');
        $res = array();

        $this->customerModel->transBegin();
        try {
            if (!$foto->isValid()) throw new Exception("Foto tidak valid!");
            if (empty($nama)) throw new Exception("Nama dibutuhkan!");
            if (empty($alamat)) throw new Exception("Alamat masih kosong!");
            if (empty($telepon)) throw new Exception("Telephone masih kosong!");
            if (empty($email)) throw new Exception("Email masih kosong!");

            // Validasi ekstensi file
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $extension = $foto->getExtension();
            if (!in_array($extension, $allowedExtensions)) {
                throw new Exception("Format foto tidak valid, hanya jpg, jpeg, dan png yang diperbolehkan!");
            }

            // Generate nama file unik untuk foto
            $newName = $foto->getRandomName();
            $foto->move('uploads/customers/', $newName); // Pindahkan file ke folder uploads/customers/
            $filePath = 'uploads/customers/' . $newName; // Path file yang disimpan

            // Simpan data ke database
            $this->customerModel->store([
                'filepath' => $filePath,
                'customername' => $nama,
                'address' => $alamat,
                'phone' => $telepon,
                'email' => $email,
                'createddate' => date('Y-m-d H:i:s'),
                'createdby' => getSession('userid'),
                'updateddate' => date('Y-m-d H:i:s'),
                'updatedby' => getSession('userid'),
            ]);

            $res = [
                'sukses' => '1',
                'pesan' => 'Sukses menambahkan Customer',
                'dbError' => db_connect()
            ];
            $this->customerModel->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => $e->getMessage(),
                'traceString' => $e->getTraceAsString(),
                'dbError' => db_connect()->error()
            ];
            $this->customerModel->transRollback();
        }
        $this->customerModel->transComplete();
        echo json_encode($res);
    }

    public function updateData()
    {
        $customerid = $this->request->getPost('customerid');
        $foto = $this->request->getFile('foto');
        $nama = $this->request->getPost('nama');
        $alamat = $this->request->getPost('alamat');
        $telepon = $this->request->getPost('telepon');
        $email = $this->request->getPost('email');
        $res = array();

        $this->customerModel->transBegin();
        try {
            if (empty($customerid)) throw new Exception("ID customer tidak ditemukan!");
            if (empty($nama)) throw new Exception("Nama masih kosong!");
            if (empty($alamat)) throw new Exception("Alamat masih kosong!");
            if (empty($telepon)) throw new Exception("Telepon masih kosong!");
            if (empty($email)) throw new Exception("Email masih kosong!");

            $data = [
                'customername' => $nama,
                'address' => $alamat,
                'phone' => $telepon,
                'email' => $email,
                'updateddate' => date('Y-m-d H:i:s'),
                'updatedby' => getSession('userid'),
            ];

            if ($foto->isValid()) {
                // Validasi ekstensi file
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $extension = $foto->getExtension();
                if (!in_array($extension, $allowedExtensions)) {
                    throw new Exception("Format foto tidak valid, hanya jpg, jpeg, dan png yang diperbolehkan!");
                }

                // Hapus file lama jika ada
                $oldFilePath = $this->customerModel->getOne($customerid)['filepath'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }

                // Simpan file baru
                $newName = $foto->getRandomName();
                $foto->move('uploads/customers/', $newName);
                $data['filepath'] = 'uploads/customers/' . $newName;
            }

            $this->customerModel->edit($data, $customerid);
            $res = [
                'sukses' => '1',
                'pesan' => 'Sukses update user baru',
                'dbError' => db_connect()
            ];
            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => $e->getMessage(),
                'traceString' => $e->getTraceAsString(),
                'dbError' => db_connect()->error()
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        echo json_encode($res);
    }

    public function deleteData()
    {
        $customerid = $this->request->getPost('id');
        $res = array();
        $this->db->transBegin();
        try {
            if (empty($customerid)) throw new Exception("ID Customer tidak ditemukan!");

            $customerid = decrypting($customerid);
            $row = $this->customerModel->getOne($customerid);

            if (empty($row)) throw new Exception("User tidak terdaftar di sistem!");

            $this->customerModel->destroy('id', $customerid);

            $res = [
                'sukses' => '1',
                'pesan' => 'Data berhasil dihapus!',
                'dbError' => db_connect()->error()
            ];
            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => $e->getMessage(),
                'traceString' => $e->getTraceAsString(),
                'dbError' => db_connect()->error()
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        echo json_encode($res);
    }

    public function exportExcel()
    {
        $customer = $this->customerModel->datatable()->get()->getResultArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Data Customer')
            ->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A1:F1');

        $sheet->setCellValue('A2', 'No')
            ->setCellValue('B2', 'Foto')
            ->setCellValue('C2', 'Nama')
            ->setCellValue('D2', 'Alamat')
            ->setCellValue('E2', 'Telephone')
            ->setCellValue('F2', 'Email');

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(55);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(30);

        $borderArray = [
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN],
                'bottom' => ['borderStyle' => Border::BORDER_THIN],
                'left' => ['borderStyle' => Border::BORDER_THIN],
                'right' => ['borderStyle' => Border::BORDER_THIN],
            ]
        ];
        $sheet->getStyle('A2:F2')->applyFromArray($borderArray);
        $sheet->getStyle('A2:F2')->getFont()->setBold(true);


        $row = 3;
        foreach ($customer as $index => $rowData) {
            $sheet->setCellValue("A$row", $index + 1)
                ->setCellValue("B$row", $rowData['filepath'])
                ->setCellValue("C$row", $rowData['customername'])
                ->setCellValue("D$row", $rowData['address'])
                ->setCellValue("E$row", $rowData['phone'])
                ->setCellValue("F$row", $rowData['email']);

            $sheet->getStyle("A$row:F$row")->applyFromArray($borderArray);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'data_customer.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function printPDF()
    {
        $pdf = new Fpdf();
        $pdf->AddPage('L'); // Set landscape orientation
        $pdf->SetFont('Arial', 'B', 12);

        // Header
        $pdf->Cell(10, 10, 'No', 1, 0, 'C');
        $pdf->Cell(50, 10, 'Filepath', 1, 0, 'C');
        $pdf->Cell(50, 10, 'Nama', 1, 0, 'C');
        $pdf->Cell(80, 10, 'Email', 1, 0, 'C');
        $pdf->Cell(60, 10, 'Alamat', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Telepon', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 12);
        $datas = $this->customerModel->datatable()->get()->getResultArray();

        $no = 1;
        foreach ($datas as $row) {
            $pdf->Cell(10, 10, $no++, 1, 0, 'C');

            // Filepath
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->MultiCell(50, 10, $row['filepath'], 1, 'L');
            $pdf->SetXY($x + 50, $y);

            // Nama
            $x = $pdf->GetX();
            $pdf->MultiCell(50, 10, $row['customername'], 1, 'L');
            $pdf->SetXY($x + 50, $y);

            // Email
            $x = $pdf->GetX();
            $pdf->MultiCell(80, 10, $row['email'], 1, 'L');
            $pdf->SetXY($x + 80, $y);

            // Alamat
            $x = $pdf->GetX();
            $pdf->MultiCell(60, 10, $row['address'], 1, 'L');
            $pdf->SetXY($x + 60, $y);

            // Telepon
            $pdf->Cell(40, 10, $row['phone'], 1, 1, 'L');
        }

        $pdf->Output('D', 'data_customer.pdf');
        exit;
    }

    public function logOut()
    {
        $this->db->transBegin();
        try {
            session()->destroy();
            $res = [
                'sukses' => '1',
                'pesan' => 'Berhasil Logout',
                'link' => ('login/v_login')
            ];
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => $e->getMessage(),
                'traceString' => $e->getTraceAsString()
            ];
        }
        $this->db->transComplete();
        echo json_encode($res);
    }
}
