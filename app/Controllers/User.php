<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\MUser;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Fpdf\Fpdf;

class User extends BaseController
{
    protected $userModel;
    protected $bc;
    protected $db;
    public function __construct()
    {
        $this->userModel = new MUser();
        $this->bc = [
            [
                'Setting',
                'User'
            ]
        ];
    }

    public function index()
    {
        return view('master/user/v_user', [
            'title' => 'User',
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
            $row = $this->userModel->getByName($username);
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
        $table = Datatables::method([MUser::class, 'datatable'], 'searchable')
            ->make();

        $table->updateRow(function ($db, $no) {
            $btn_edit = "<button type='button' class='btn btn-sm btn-warning' onclick=\"modalForm('Update User - " . $db->fullname . "', 'modal-lg', '" . getURL('user/form/' . encrypting($db->id)) . "', {identifier: this})\"><i class='bx bx-edit-alt'></i></button>";
            $btn_hapus = "<button type='button' class='btn btn-sm btn-danger' onclick=\"modalDelete('Delete User - " . $db->fullname . "', {'link':'" . getURL('user/delete') . "', 'id':'" . encrypting($db->id) . "', 'pagetype':'table'})\"><i class='bx bx-trash'></i></button>";
            return [
                $no,
                $db->fullname,
                $db->username,
                $db->email,
                $db->telp,
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
            $row = $this->userModel->getOne($id);
        }
        $dt['view'] = view('master/user/v_form', [
            'form_type' => $form_type,
            'row' => $row,
            'userid' => $id
        ]);
        $dt['csrfToken'] = csrf_hash();
        echo json_encode($dt);
    }

    public function addData()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $fullname = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $phone = $this->request->getPost('phone');
        $res = array();

        $this->db->transBegin();
        try {
            if (empty($username)) throw new Exception("Username dibutuhkan!");
            if (empty($password)) throw new Exception("Password dibutuhkan!");
            if (empty($fullname)) throw new Exception("Fullname masih kosong!");
            $row = $this->userModel->getByName($fullname);
            if (!empty($row)) throw new Exception("User dengan username ini sudah terdaftar!");
            $this->userModel->store([
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'fullname' => $fullname,
                'email' => $email,
                'telp' => $phone,
                'createddate' => date('Y-m-d H:i:s'),
                'createdby' => 1,
                'updateddate' => date('Y-m-d H:i:s'),
                'updatedby' => 1,
            ]);
            $res = [
                'sukses' => '1',
                'pesan' => 'Sukses menambahkan user baru',
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

    public function updateData()
    {
        $userid = $this->request->getPost('id');
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $fullname = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $phone = $this->request->getPost('phone');
        $res = array();

        $this->db->transBegin();
        try {
            if (empty($username)) throw new Exception("Username dibutuhkan!");
            if (empty($fullname)) throw new Exception("Fullname masih kosong!");
            $row = $this->userModel->getByName($fullname);
            if (!empty($row)) throw new Exception("User dengan username ini sudah terdaftar!");
            $data = [
                'username' => $username,
                'fullname' => $fullname,
                'email' => $email,
                'telp' => $phone,
                'updateddate' => date('Y-m-d H:i:s'),
                'updatedby' => $userid,
            ];
            if (!empty($password)) {
                $data += [
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                ];
            }
            $this->userModel->edit($data, $userid);
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
        $userid = decrypting($this->request->getPost('id'));
        $res = array();
        $this->db->transBegin();
        try {
            $row = $this->userModel->getOne($userid);
            if (empty($row)) throw new Exception("User tidak terdaftar!");
            $this->userModel->destroy('id', $userid);
            $res = [
                'sukses' => '1',
                'pesan' => 'Data berhasil dihapus!',
                'dbError' => db_connect()->error()
            ];
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
        $userid = session()->get('userid');
        $row = $this->userModel->getOne($userid);
        if (!empty($row)) {
            destroySession();
        }
        return redirect('login');
    }

    public function printPDF()
    {
        $pdf = new Fpdf();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);

        $pdf->Cell(10, 10, 'No', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Name', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Username', 1, 0, 'C');
        $pdf->Cell(60, 10, 'Email', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Telephone', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 12);
        $datas = $this->userModel->datatable()->get()->getResultArray();

        $no = 1;
        foreach ($datas as $row) {
            $pdf->Cell(10, 10, $no++, 1, 0, 'C');
            $pdf->Cell(40, 10, $row['fullname'], 1, 0, 'L');
            $pdf->Cell(40, 10, $row['username'], 1, 0, 'L');
            $pdf->Cell(60, 10, $row['email'], 1, 0, 'L');
            $pdf->Cell(40, 10, $row['telp'], 1, 1, 'L');
        }

        $pdf->Output('D', 'user_data.pdf');
        exit;
    }
}
