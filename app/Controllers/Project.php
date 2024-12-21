<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\MProject;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class Project extends BaseController
{
    protected $projectModel;

    public function __construct()
    {
        $this->projectModel = new MProject();
        $this->bc = [
            [
                'Setting',
                'Project'
            ]
        ];
    }

    public function index()
    {
        return view('master/project/v_project', [
            'title' => 'Project',
            'akses' => null,
            'breadcrumb' => $this->bc,
            'section' => 'Setting Project',
        ]);
    }

    public function datatable()
    {
        $table = Datatables::method([MProject::class, 'datatable'], 'searchable')
            ->make();

        $table->updateRow(function ($db, $no) {
            $btn_edit = "<button type='button' class='btn btn-sm btn-warning' onclick=\"modalForm('Update Project - " . $db->projectname . "', 'modal-lg', '" . getURL('project/form/' . encrypting($db->id)) . "', {identifier: this})\"><i class='bx bx-edit-alt'></i></button>";
            $btn_hapus = "<button type='button' class='btn btn-sm btn-danger' onclick=\"modalDelete('Delete Project - " . $db->projectname . "', {'link':'" . getURL('project/delete') . "', 'id':'" . encrypting($db->id) . "', 'pagetype':'table'})\"><i class='bx bx-trash'></i></button>";
            return [
                $no,
                $db->projectname,
                $db->description,
                $db->startdate,
                $db->enddate,
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
            $row = $this->projectModel->getOne($id);

            // Check if the data exists
            if (empty($row)) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException("Project with ID $id not found.");
            }
        }

        $row['startdate'] = $row['startdate'] ?? '';
        $row['enddate'] = $row['enddate'] ?? '';

        $dt['view'] = view('master/project/v_form', [
            'form_type' => $form_type,
            'row' => $row,
            'projectid' => $id
        ]);
        $dt['csrfToken'] = csrf_hash();
        echo json_encode($dt);
    }

    public function addData()
    {
        $projectname = $this->request->getPost('projectname');
        $description = $this->request->getPost('description');
        $startdate = $this->request->getPost('startdate');
        $enddate = $this->request->getPost('enddate');
        $res = [];

        $this->db->transBegin();
        try {
            if (empty($projectname)) throw new Exception("Project Name is required!");
            if (empty($description)) throw new Exception("Description is required!");
            if (empty($startdate)) throw new Exception("Start Date is required!");
            if (empty($enddate)) throw new Exception("End Date is required!");

            $this->projectModel->store([
                'projectname' => $projectname,
                'description' => $description,
                'startdate' => $startdate,
                'enddate' => $enddate,
                'createddate' => date('Y-m-d H:i:s'),
                'createdby' => 1, // Adjust for actual user
                'updateddate' => date('Y-m-d H:i:s'),
                'updatedby' => 1, // Adjust for actual user
            ]);
            $res = [
                'sukses' => '1',
                'pesan' => 'Project added successfully!',
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
        $projectid = $this->request->getPost('id');
        $projectname = $this->request->getPost('projectname');
        $description = $this->request->getPost('description');
        $startdate = $this->request->getPost('startdate');
        $enddate = $this->request->getPost('enddate');
        $res = [];

        $this->db->transBegin();
        try {
            if (empty($projectname)) throw new Exception("Project Name is required!");
            if (empty($description)) throw new Exception("Description is required!");
            if (empty($startdate)) throw new Exception("Start Date is required!");
            if (empty($enddate)) throw new Exception("End Date is required!");

            $data = [
                'projectname' => $projectname,
                'description' => $description,
                'startdate' => $startdate,
                'enddate' => $enddate,
                'updateddate' => date('Y-m-d H:i:s'),
                'updatedby' => 1, // Adjust for actual user
            ];

            $this->projectModel->edit($data, $projectid);

            $res = [
                'sukses' => '1',
                'pesan' => 'Project updated successfully!',
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
        $projectId = decrypting($this->request->getPost('id'));
        $res = [];

        $this->db->transBegin();
        try {
            $row = $this->projectModel->getOne($projectId);
            if (empty($row)) throw new Exception("Project not found!");

            $this->projectModel->destroy('id', $projectId);

            $res = [
                'sukses' => '1',
                'pesan' => 'Project deleted successfully!',
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

    public function logOut()
    {
        $userid = session()->get('userid');
        $row = $this->userModel->getOne($userid);
        if (!empty($row)) {
            destroySession();
        }
        return redirect('login');
    }
}
