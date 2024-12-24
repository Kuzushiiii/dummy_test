<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\MCategory;
use App\Models\MUser;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;

class Category extends BaseController
{
    protected $categoryModel;
    protected $bc;
    protected $db;

    public function __construct()
    {
        $this->categoryModel = new MCategory();
        $this->bc = [
            [
                'Setting',
                'Category'
            ]
        ];
    }

    public function index()
    {
        return view('master/category/v_category', [
            'title' => 'Category',
            'akses' => null,
            'breadcrumb' => $this->bc,
            'section' => 'Setting Category',
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
        $table = Datatables::method([MCategory::class, 'datatable'], 'searchable')
            ->make();

        $table->updateRow(function ($db, $no) {
            $btn_edit = "<button type='button' class='btn btn-sm btn-warning' onclick=\"modalForm('Update Category - " . $db->categoryname . "', 'modal-lg', '" . getURL('category/form/' . encrypting($db->id)) . "', {identifier: this})\"><i class='bx bx-edit-alt'></i></button>";
            $btn_hapus = "<button type='button' class='btn btn-sm btn-danger' onclick=\"modalDelete('Delete Category - " . $db->categoryname . "', {'link':'" . getURL('category/delete') . "', 'id':'" . encrypting($db->id) . "', 'pagetype':'table'})\"><i class='bx bx-trash'></i></button>";
            return [
                $no,
                $db->categoryname,
                $db->description,
                $db->filepath,
                "<div style='display:flex;align-items:center;justify-content:center;'>$btn_edit&nbsp;$btn_hapus</div>"
            ];
        });

        $table->toJson();
    }

    public function forms($categoryid = '')
    {
        $form_type = (empty($categoryid) ? 'add' : 'edit');
        $row = [];
        if ($categoryid != '') {
            $categoryid = decrypting($categoryid);
            $row = $this->categoryModel->find($categoryid); // Use find method to get a single record
        }
        $dt['view'] = view('master/category/v_form', [
            'form_type' => $form_type,
            'row' => $row,
            'id' => $categoryid
        ]);
        $dt['csrfToken'] = csrf_hash();
        echo json_encode($dt);
    }

    public function export()
    {
        $categories = $this->categoryModel->findAll(); // Ensure this returns an array or object
    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Define header style
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4CAF50'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
    
        // Define data style
        $dataStyle = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
    
        // Set header values and apply style
        $sheet->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Category Name')
            ->setCellValue('C1', 'Description')
            ->setCellValue('D1', 'Filepath');
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);
    
        // Auto size columns
        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    
        // Set data values and apply style
        $row = 2;
        foreach ($categories as $index => $category) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $category['categoryname']);
            $sheet->setCellValue('C' . $row, $category['description']);
            $sheet->setCellValue('D' . $row, $category['filepath']);
            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($dataStyle);
            $row++;
        }
    
        $writer = new Xlsx($spreadsheet);
        $filename = 'category.xlsx';
    
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    public function addData()
    {
        $categoryname = $this->request->getPost('namakategori');
        $description = $this->request->getPost('deskripsi');
        $filepath = $this->request->getFile('foto');
        $res = array();

        $this->categoryModel->transBegin();
        try {
            if (!$filepath->isValid()) throw new Exception("filepath tidak valid!");
            if (empty($categoryname)) throw new Exception("Nama kategori dibutuhkan!");
            if (empty($description)) throw new Exception("Deskripsi masih kosong!");

            // Validasi ekstensi file
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $extension = $filepath->getExtension();
            if (!in_array($extension, $allowedExtensions)) {
                throw new Exception("Format filepath tidak valid, hanya jpg, jpeg, dan png yang diperbolehkan!");
            }

            // Generate nama file unik untuk filepath
            $newName = $filepath->getRandomName();
            $filepath->move('uploads/category/', $newName); // Pindahkan file ke folder uploads/categorys/
            $filePath = 'uploads/category/' . $newName; // Path file yang disimpan

            // Simpan data ke database
            $this->categoryModel->insert([
                'filepath' => $filePath,
                'categoryname' => $categoryname,
                'description' => $description,
                'createddate' => date('Y-m-d H:i:s'),
                'createdby' => getSession('userid'),
                'updateddate' => date('Y-m-d H:i:s'),
                'updatedby' => getSession('userid'),
            ]);

            $res = [
                'sukses' => '1',
                'pesan' => 'Sukses menambahkan category',
                'dbError' => db_connect()
            ];
            $this->categoryModel->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => $e->getMessage(),
                'traceString' => $e->getTraceAsString(),
                'dbError' => db_connect()->error()
            ];
            $this->categoryModel->transRollback();
        }
        $this->categoryModel->transComplete();
        echo json_encode($res);
    }

    public function updateData()
    {
        $categoryid = $this->request->getPost('categoryid'); // Retrieve category ID from POST data
        $categoryname = $this->request->getPost('namakategori');
        $description = $this->request->getPost('deskripsi');
        $filepath = $this->request->getFile('foto');
        $res = array();

        $this->categoryModel->transBegin();
        try {
            if (empty($categoryid)) throw new Exception("ID category kosong!");
            if (empty($categoryname)) throw new Exception("Nama masih kosong!");
            if (empty($description)) throw new Exception("Deskripsi masih kosong!");

            $data = [
                'categoryname' => $categoryname,
                'description' => $description,
                'updateddate' => date('Y-m-d H:i:s'),
                'updatedby' => getSession('userid'),
            ];

            if ($filepath && $filepath->isValid()) {
                // Validasi ekstensi file
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $extension = $filepath->getExtension();
                if (!in_array($extension, $allowedExtensions)) {
                    throw new Exception("Format foto tidak valid, hanya jpg, jpeg, dan png yang diperbolehkan!");
                }

                // Hapus file lama jika ada
                $oldFilePath = $this->categoryModel->find($categoryid)['filepath'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }

                // Simpan file baru
                $newName = $filepath->getRandomName();
                $filepath->move('uploads/category/', $newName);
                $data['filepath'] = 'uploads/category/' . $newName;
            }

            $this->categoryModel->update($categoryid, $data);
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
        $categoryid = $this->request->getPost('id');
        $res = array();
        $this->db->transBegin();
        try {
            if (empty($categoryid)) throw new Exception("ID category tidak ditemukan!");

            $categoryid = decrypting($categoryid);
            $row = $this->categoryModel->find($categoryid);

            if (empty($row)) throw new Exception("User tidak terdaftar di sistem!");

            $this->categoryModel->delete($categoryid);

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
}