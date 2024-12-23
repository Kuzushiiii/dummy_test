<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\MSupplier;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

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
        $row = [];
        if ($id != '') {
            $id = decrypting($id); // Ensure this function exists and works correctly
            $row = $this->MSupplier->find($id); // Use find method to get a single record
        }
        $dt['view'] = view('master/supplier/v_form', [
            'form_type' => $form_type,
            'row' => $row,
            'userid' => $id,
            'title' => 'Supplier Form' // Pass the title variable
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
        $filepath = $this->request->getPost('filepath');
        $res = array();

        $this->db->transBegin();
        try {
            // Validasi input
            if (empty($suppliername)) throw new Exception('Masukkan nama supplier');
            if (empty($address)) throw new Exception('Masukkan alamat');
            if (empty($phone)) throw new Exception('Masukkan nomor HP');
            if (empty($email)) throw new Exception('Masukkan email');
            if (empty($filepath)) throw new Exception('Masukkan file path');

            // Insert data
            $this->MSupplier->store([
                'suppliername' => $suppliername,
                'address' => $address,
                'phone' => $phone,
                'email' => $email,
                'filepath' => $filepath,
                'createdby' => 1, 
                'createddate' => date('Y-m-d H:i:s'),
                'updatedby' => 1,
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
        $table = Datatables::method([MSupplier::class, 'datatable'], 'searchable')
            ->make();

        $table->updateRow(function ($db, $no) {
            $btn_edit = "<button type='button' class='btn btn-sm btn-warning' onclick=\"modalForm('Update User - " . $db->suppliername . "', 'modal-lg', '" . getURL('supplier/form/' . encrypting($db->id)) . "', {identifier: this})\"><i class='bx bx-edit-alt'></i></button>";
            $btn_hapus = "<button type='button' class='btn btn-sm btn-danger' onclick=\"modalDelete('Delete User - " . $db->suppliername . "', {'link':'" . getURL('supplier/delete') . "', 'id':'" . encrypting($db->id) . "', 'pagetype':'table'})\"><i class='bx bx-trash'></i></button>";
            return [
                $no,
                $db->suppliername,
                $db->address,
                $db->phone,
                $db->email,
                $db->filepath,
                "<div style='display:flex;align-items:center;justify-content:center;'>$btn_edit&nbsp;$btn_hapus</div>"
            ];
        });
        $table->toJson();
    }

    public function update()
    {
        $userid = $this->request->getPost('id');
        $suppliername = $this->request->getPost('suppliername');
        $address = $this->request->getPost('address');
        $phone = $this->request->getPost('phone');
        $email = $this->request->getPost('email');
        $filepath = $this->request->getPost('filepath');
        $res = array();

        $this->db->transBegin();
        try {
            // Validasi input
            if (empty($suppliername)) throw new Exception('Masukkan nama supplier');
            if (empty($address)) throw new Exception('Masukkan alamat');
            if (empty($phone)) throw new Exception('Masukkan nomor HP');
            if (empty($email)) throw new Exception('Masukkan email');
            if (empty($filepath)) throw new Exception('Masukkan file path');
            $data = [
                'suppliername' => $suppliername,
                'address' => $address,
                'phone' => $phone,
                'email' => $email,
                'filepath' => $filepath,
                'updatedby' => 1,
                'updateddate' => date('Y-m-d H:i:s')
            ];

            $this->MSupplier->edit($data, $userid);
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
            if (empty($row)) throw new Exception("User not found!");
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

}