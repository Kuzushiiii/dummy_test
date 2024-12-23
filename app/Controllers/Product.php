<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\MProduct;
use CodeIgniter\HTTP\ResponseInterface;
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
                ? "<img src='" . htmlspecialchars($db->filepath) .  "' alt='foto product' width='50' style='border-radius: 50%; object-fit: cover;'>"
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
                'createdby' => 1, // Adjust for actual user
                'updateddate' => date('Y-m-d H:i:s'),
                'updatedby' => 1, // Adjust for actual user
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
                'updatedby' => 1, // Adjust for actual user
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
}
