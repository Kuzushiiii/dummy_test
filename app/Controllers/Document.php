<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\MDocument;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;


class Document extends BaseController
{
    protected $MDocument;
    protected $bc;
    protected $db;
    public function __construct()
    {
        $this->MDocument = new MDocument();
        $this->bc = [
            [
                'Setting',
                'Document'
            ]
        ];
    }

    public function index()
    {
        return view('master/document/v_document', [
            'title' => 'Document',
            'akses' => null,
            'breadcrumb' => $this->bc,
            'section' => 'Setting Document',
        ]);
    }




    public function datatable()
    {
        $table = Datatables::method([MDocument::class, 'datatable'], 'searchable')
            ->make();

        $table->updateRow(function ($db, $no) {
            $btn_edit = "<button type='button' class='btn btn-sm btn-warning' onclick=\"modalForm('Update document - " . $db->documentname . "', 'modal-lg', '" . getURL('document/form/' . encrypting($db->id)) . "', {identifier: this})\"><i class='bx bx-edit-alt'></i></button>";
            $btn_hapus = "<button type='button' class='btn btn-sm btn-danger' onclick=\"modalDelete('Delete document - " . $db->documentname . "', {'link':'" . getURL('document/delete') . "', 'id':'" . encrypting($db->id) . "', 'pagetype':'table'})\"><i class='bx bx-trash'></i></button>";
            return [
                $no,
                $db->documentname,
                $db->description,
                $db->filepath,
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
            $row = $this->MDocument->getOne($id);
        }
        $dt['view'] = view('master/document/v_form', [
            'form_type' => $form_type,
            'row' => $row,
            'userid' => $id
        ]);
        $dt['csrfToken'] = csrf_hash();
        echo json_encode($dt);
    }

    public function addData()
    {
        $documentname = $this->request->getPost('name');
        $description = $this->request->getPost('description');
        $filepath = $this->request->getFile('dokumen');
        $res = array();
        $db = db_connect();

        $db->transBegin();
        try {
            if (empty($description)) throw new Exception("Masukkan deskripsi");
            if (!$filepath || !$filepath->isValid()) throw new Exception("Filepath tidak valid!");
            if (empty($documentname)) throw new Exception("Masukkan nama dokumen");

            $allowedExtensions = ['doc', 'docx', 'pdf', 'xlsx'];
            $extension = $filepath->getExtension();
            if (!in_array($extension, $allowedExtensions)) {
                throw new Exception("Format file tidak valid, hanya doc, docx, pdf, dan xlsx yang diperbolehkan!");
            }

            // Generate nama file unik untuk filepath
            $newName = $filepath->getRandomName();
            $filepath->move('uploads/document', $newName);
            $filePath = 'uploads/document' . $newName;

            $this->MDocument->store([
                'documentname' => $documentname,
                'description' => $description,
                'filepath' => $filePath,
                'createddate' => date('Y-m-d H:i:s'),
                'createdby' => 1,
                'updateddate' => date('Y-m-d H:i:s'),
                'updatedby' => 1,
            ]);

            $db->transCommit();

            $res = [
                'sukses' => '1',
                'pesan' => 'Sukses menambahkan dokumen baru',

            ];
        } catch (Exception $e) {
            $db->transRollback();
            $res = [
                'sukses' => '0',
                'pesan' => $e->getMessage(),
                'traceString' => $e->getTraceAsString(),
            ];
        }

        echo json_encode($res);
    }



    public function updateData()
    {
        
        $userid = $this->request->getPost('id');
        $documentname = $this->request->getPost('name');
        $description = $this->request->getPost('description');
        $filepath = $this->request->getFile('dokumen');
        $res = array();

        $this->db->transBegin();
        try {
            if (empty($documentname)) throw new Exception("Nama dokumen dibutuhkan!");
            if (empty($description)) throw new Exception("Deskripsi masih kosong!");
            $row = $this->MDocument->getByName($documentname);
            $data = [
                'documentname' => $documentname,
                'description' => $description,
                'dokumen' => $filepath,
                'updateddate' => date('Y-m-d H:i:s'),
                'updatedby' => $userid,
            ];

            if ($filepath && $filepath->isValid()) {
                // Validasi ekstensi file
                $allowedExtensions = ['doc', 'docx', 'pdf', 'xlsx'];
                $extension = $filepath->getExtension();
                if (!in_array($extension, $allowedExtensions)) {
                    throw new Exception("Format foto tidak valid, hanya docx, doc, pdf, xlsx diperbolehkan!");
                }
    
                // Hapus file lama jika ada
                $oldFilePath = $this->MDocument->getOne($userid)['filepath'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }

                // Simpan file baru
                $newName = $filepath->getRandomName();
                $filepath->move('uploads/document', $newName);
                $data['filepath'] = 'uploads/document' . $newName;
            
    
                // Hapus file lama jika ada
                $oldFilePath = $this->MDocument->getOne($userid)['filepath'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $this->MDocument->edit($data, $userid);
            $res = [
                'sukses' => '1',
                'pesan' => 'Sukses update dokumen baru',
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
            $row = $this->MDocument->getOne($userid);
            if (empty($row)) throw new Exception("Dokumen tidak terdaftar!");
            $this->MDocument->destroy('id', $userid);
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
        $row = $this->MDocument->getOne($userid);
        if (!empty($row)) {
            destroySession();
        }
        return redirect('login');
    }
}
