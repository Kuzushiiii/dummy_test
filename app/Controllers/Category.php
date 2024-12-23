<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\MCategory;
use App\Models\MUser;
use CodeIgniter\HTTP\ResponseInterface;
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
            $row = $this->categoryModel->getByName($username);
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
            $row = $this->categoryModel->getOne($categoryid);
        }
        $dt['view'] = view('master/category/v_form', [
            'form_type' => $form_type,
            'row' => $row,
            'id' => $categoryid
        ]);
        $dt['csrfToken'] = csrf_hash();
        echo json_encode($dt);
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
            $filepath->move('uploads/category/', $newName); // Pindahkan file ke folder uploads/customers/
            $filePath = 'uploads/category/' . $newName; // Path file yang disimpan

            // Simpan data ke database
            $this->categoryModel->store([
                'filepath' => $filePath,
                'categoryname' => $categoryname,
                'description' => $description,
                'createddate' => date('Y-m-d H:i:s'),
                'createdby' => 1,
                'updateddate' => date('Y-m-d H:i:s'),
                'updatedby' => 1,
            ]);

            $res = [
                'sukses' => '1',
                'pesan' => 'Sukses menambahkan Customer',
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
                'updatedby' => 1,
            ];
    
            if ($filepath && $filepath->isValid()) {
                // Validasi ekstensi file
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $extension = $filepath->getExtension();
                if (!in_array($extension, $allowedExtensions)) {
                    throw new Exception("Format foto tidak valid, hanya jpg, jpeg, dan png yang diperbolehkan!");
                }
    
                // Hapus file lama jika ada
                $oldFilePath = $this->categoryModel->getOne($categoryid)['filepath'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
    
                // Simpan file baru
                $newName = $filepath->getRandomName();
                $filepath->move('uploads/category/', $newName);
                $data['filepath'] = 'uploads/category/' . $newName;
            }
    
            $this->categoryModel->edit($data, $categoryid);
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
            $row = $this->categoryModel->getOne($customerid);

            if (empty($row)) throw new Exception("User tidak terdaftar di sistem!");

            $this->categoryModel->destroy('id', $customerid);

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
