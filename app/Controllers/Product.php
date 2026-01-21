<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\MProduct;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Fpdf\Fpdf;
use Exception;

class Product extends BaseController
{
    protected $db;
    protected $bc;
    protected $productModel;

    public function __construct()
    {
        $this->productModel = new MProduct();
        $this->bc = [
            [
                'Setting',
                'Product'
            ]
        ];
    }

    public function index()
    {
        return view('master/product/v_product', [
            'title' => 'Product',
            'akses' => null,
            'breadcrumb' => $this->bc,
            'section' => 'Setting Product',
        ]);
    }

    public function datatable()
    {
        $table = Datatables::method([MProduct::class, 'datatable'], 'searchable')
            ->make();

        $table->updateRow(function ($db, $no) {
            $btn_edit = "<button type='button' class='btn btn-sm btn-warning' onclick=\"modalForm('Update Product - " . $db->productname . "', 'modal-lg', '" . getURL('product/form/' . encrypting($db->id)) . "', {identifier: this})\"><i class='bx bx-edit-alt'></i></button>";
            $btn_hapus = "<button type='button' class='btn btn-sm btn-danger' onclick=\"modalDelete('Delete Product - " . $db->productname . "', {'link':'" . getURL('product/delete') . "', 'id':'" . encrypting($db->id) . "', 'pagetype':'table'})\"><i class='bx bx-trash'></i></button>";

            $foto_product = !empty($db->filepath)
                ? "<center><img src='" . htmlspecialchars($db->filepath) .  "' alt='foto product' width='50' style='border-radius: 50%; object-fit: cover;'></center>"
                : "<img( src:'path/to/default.png' alt='foto product' width='50' height:'50' style='border-radius:50%; object-fit: cover;'>";
            return [
                $no,
                $db->productname,
                $db->category,
                $db->price,
                $db->stock,
                $foto_product,
                "<div style='display:flex;align-items:center;justify-content:center;'>$btn_edit&nbsp;$btn_hapus</div>"
            ];
        });
        $table->toJson();
    }

    public function forms($id = '')
    {
        $form_type = (empty($id) ? 'add' : 'edit');
        $row = [];
        if ($id != '') {
            $id = decrypting($id);
            $row = $this->productModel->getOne($id);
            // Check if the data exists
            if (empty($row)) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException("Product with ID $id not found.");
            }
        }

        $dt['view'] = view('master/product/v_form', [
            'form_type' => $form_type,
            'row' => $row,
            'productid' => $id
        ]);
        $dt['csrfToken'] = csrf_hash();
        echo json_encode($dt);
    }

    public function addData()
    {
        $productname = $this->request->getPost('productname');
        $category = $this->request->getPost('category');
        $price = $this->request->getPost('price');
        $stock = $this->request->getPost('stock');
        $filepath = $this->request->getFile('filepath');
        $res = [];

        $this->db->transBegin();
        try {
            if (empty($productname)) throw new Exception("Product is required!");
            if (empty($category)) throw new Exception("category is required!");
            if (empty($price)) throw new Exception("price is required!");
            if (empty($stock)) throw new Exception("stokc is required!");
            if (empty($filepath->isValid())) throw new Exception("img is required!");

            // Check for duplicate product name
            $existing = $this->productModel->where('productname', $productname)->first();
            if ($existing) throw new Exception("Product name already exists!");

            $allowedExceptions = ['jpg', 'jpeg', 'png'];
            $extension = $filepath->getExtension();
            if (!in_array($extension, $allowedExceptions)) {
                throw new Exception("Invalid file type. Only ");
            }
            $newName = $filepath->getExtension();
            $filepath->move('upload/product/', $newName);
            $filepath = 'upload/product/' . $newName;

            $this->productModel->store([
                'productname' => $productname,
                'category' => $category,
                'price' => $price,
                'stock' => $stock,
                'filepath' => $filepath,
                'createddate' => date('Y-m-d H:i:s'),
                'createdby' => getSession('userid'), // Adjust for actual user
                'updateddate' => date('Y-m-d H:i:s'),
                'updatedby' => getSession('userid'), // Adjust for actual user
            ]);
            $res = [
                'sukses' => '1',
                'pesan' => 'Product added successfully!',
                'dbError' => db_connect()
            ];
            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => $e->getMessage(),
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        echo json_encode($res);
    }

    public function updateData()
    {
        $productid = $this->request->getPost('id');
        $productname = $this->request->getPost('productname');
        $category = $this->request->getPost('category');
        $price = $this->request->getPost('price');
        $stock = $this->request->getPost('stock');
        $filepath = $this->request->getFile('filepath');
        $res = [];

        $this->db->transBegin();
        try {
            // Validasi data yang diperlukan
            if (empty($productname)) throw new Exception("Product Name is required!");
            if (empty($category)) throw new Exception("Category is required!");
            if (empty($price)) throw new Exception("Price is required!");
            if (empty($stock)) throw new Exception("Stock is required!");

            // Ambil data produk lama untuk mendapatkan gambar sebelumnya
            $oldData = $this->productModel->getOne($productid);
            if (empty($oldData)) throw new Exception("Product not found!");

            // Jika file baru diunggah, validasi file tersebut
            $newFilePath = $oldData['filepath']; // Default ke filepath lama
            if ($filepath && $filepath->isValid() && !$filepath->hasMoved()) {
                $allowedExtensions = ['jpg', 'png', 'jpeg'];
                $extension = $filepath->getExtension();
                if (!in_array($extension, $allowedExtensions)) {
                    throw new Exception("Invalid file type. Only JPG, PNG, and JPEG are allowed.");
                }

                // Simpan file baru
                $newName = $filepath->getRandomName();
                $filepath->move('upload/product', $newName);
                $newFilePath = 'upload/product/' . $newName;

                // Hapus file lama jika ada
                if (file_exists($oldData['filepath'])) {
                    unlink($oldData['filepath']);
                }
            }

            // Update data produk
            $data = [
                'productname' => $productname,
                'category' => $category,
                'price' => $price,
                'stock' => $stock,
                'filepath' => $newFilePath, // Gunakan file baru jika diunggah, atau file lama
                'updateddate' => date('Y-m-d H:i:s'),
                'updatedby' => getSession('userid'), // Adjust for actual user
            ];

            $this->productModel->edit($data, $productid);

            $res = [
                'sukses' => '1',
                'pesan' => 'Product updated successfully!',
            ];

            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => $e->getMessage(),
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        echo json_encode($res);
    }

    public function deleteData()
    {
        $productId = decrypting($this->request->getPost('id'));
        $res = [];

        $this->db->transBegin();
        try {
            $row = $this->productModel->getOne($productId);
            if (empty($row)) throw new Exception("Product not found!");

            $this->productModel->destroy('id', $productId);

            $res = [
                'sukses' => '1',
                'pesan' => 'Product deleted successfully!',
            ];

            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => $e->getMessage(),
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        echo json_encode($res);
    }
    public function exportexcel()
    {
        $data = $this->productModel->getAll();
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Product_Data');

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
        $headers = ['product Name', 'category', 'price', 'stock', 'File Path'];
        $columns = range('A', 'E');

        foreach ($columns as $key => $column) {
            $sheet->setCellValue($column . '1', $headers[$key]);
        }
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        $i = 2;
        foreach ($data as $row) {
            $sheet->setCellValue('A' . $i, $row['productname']);
            $sheet->setCellValue('B' . $i, $row['category']);
            $sheet->setCellValue('C' . $i, $row['price']);
            $sheet->setCellValue('D' . $i, $row['stock']);
            $sheet->setCellValue('E' . $i, $row['filepath']);
            $i++;
        }
        $sheet->getStyle('A2:E' . ($i - 1))->applyFromArray($dataStyle);
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Product' . date('dmy') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function fpdf()
    {
        $pdf = new FPDF();
        $data = $this->productModel->getAll();

        $pdf->SetTitle('Product Data');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);

        $pdf->Cell(0, 10, 'Product Data', 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 10, 'No', 1, 0, 'C');
        $pdf->Cell(50, 10, 'Product Name', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Category', 1, 0, 'C');
        $pdf->Cell(25, 10, 'Price', 1, 0, 'C');
        $pdf->Cell(20, 10, 'Stock', 1, 0, 'C');
        $pdf->Cell(55, 10, 'File Path', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $i = 1;
        foreach ($data as $row) {
            $pdf->Cell(10, 10, $i, 1, 0, 'C');

            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->MultiCell(50, 10, $row['productname'], 1, 'L');
            $pdf->SetXY($x + 50, $y);
            $x = $pdf->GetX();
            $pdf->MultiCell(30, 10, $row['category'], 1, 'L');
            $pdf->SetXY($x + 30, $y);
            $pdf->Cell(25, 10, number_format($row['price'], 2), 1, 0, 'R');
            $pdf->Cell(20, 10, $row['stock'], 1, 0, 'C');
            $x = $pdf->GetX();
            $pdf->MultiCell(55, 10, $row['filepath'], 1, 'L');
            $pdf->SetY(max($pdf->GetY(), $y + 10));

            $i++;
        }

        $pdf->Output('D', 'Product_Data.pdf');
        exit;
    }

    public function formImport()
    {
        $dt['view'] = view('master/product/v_import', []);
        $dt['csrfToken'] = csrf_hash();
        echo json_encode($dt);
    }


    function importExcel()
    {
        //untuk menangkap data yang dikirim dari front end
        $datas = json_decode($this->request->getPost('datas'));
        $res = array();
        $this->db->transBegin();
        try {
            $undfhproduct = 0;
            $undfhproductarr = [];

            foreach ($datas as $dt) {

                // validasi minimal kolom
                if (
                    empty($dt[0]) || // productname
                    empty($dt[1]) || // category
                    empty($dt[2]) || // price
                    !isset($dt[3])   // stock (boleh 0)
                ) {
                    //jika terkena validasi maka produk akan tercatat dan akan dikirim ke fe datanya
                    $undfhproduct++;
                    $undfhproductarr[] = $dt[0] ?? '-';
                    continue;
                }

                // Simpan product
                $this->productModel->insert([
                    'productname' => trim($dt[0]),
                    'category'    => trim($dt[1]),
                    'price'       => (float) $dt[2],
                    'stock'       => (int) $dt[3],
                    'createddate' => date('Y-m-d H:i:s'),
                    'createdby'   => getSession('userid'),
                    'updateddate' => date('Y-m-d H:i:s'),
                    'updatedby'   => getSession('userid'),
                ]);
            }

            $res = [
                'sukses' => '1',
                'undfhproduct' => $undfhproduct,
                'undfhproductarr' => $undfhproductarr
            ];
            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'err' => $e->getMessage(),
                'traceString' => $e->getTraceAsString()
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        $res['csrfToken'] = csrf_hash();
        echo json_encode($res);
    }
}
