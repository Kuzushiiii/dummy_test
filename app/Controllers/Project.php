<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\MProject;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Fpdf\Fpdf;
use Exception;

class Project extends BaseController
{
    protected $db;
    protected $bc;
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
            $btn_print = "<button type='button' class='btn btn-sm btn-info' onclick=\"window.open('" . getURL('project/generatePdf/' . encrypting($db->id)) . "', '_blank')\"><i class='bx bx-printer'></i></button>";

            $foto_project = !empty($db->filepath)
                ? "<center><img src='" . htmlspecialchars($db->filepath) . "' alt='foto project' width='50' style='border-radius: 50%; object-fit: cover;'></center>"
                : "<img( src:'path/to/default.png' alt='foto project' width='50' height:'50' style='border-radius:50%; object-fit: cover;'>";

            return [
                $no,
                $db->projectname,
                $db->description,
                $db->startdate,
                $db->enddate,
                $foto_project,
                "<div style='display:flex;align-items:center;justify-content:center;'>$btn_edit&nbsp;$btn_hapus&nbsp;$btn_print</div>"
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
        $filepath = $this->request->getFile('filepath');
        $res = [];

        $this->db->transBegin();
        try {
            if (empty($projectname))
                throw new Exception("Project Name is required!");
            if (empty($description))
                throw new Exception("Description is required!");
            if (empty($startdate))
                throw new Exception("Start Date is required!");
            if (empty($enddate))
                throw new Exception("End Date is required!");
            if (empty($filepath->isValid()))
                throw new Exception("filepath is required!");

            $allowedExceptions = ['jpg', 'jpeg', 'png'];
            $extension = $filepath->getExtension();
            if (!in_array($extension, $allowedExceptions)) {
                throw new Exception("Invalid file type. Only ");
            }
            $newName = $filepath->getExtension();
            $filepath->move('upload/project/', $newName);
            $filepath = 'upload/project/' . $newName;

            $this->projectModel->store([
                'projectname' => $projectname,
                'description' => $description,
                'startdate' => $startdate,
                'enddate' => $enddate,
                'filepath' => $filepath,
                'createddate' => date('Y-m-d H:i:s'),
                'createdby' => getSession('userid'), // Adjust for actual user
                'updateddate' => date('Y-m-d H:i:s'),
                'updatedby' => getSession('userid'), // Adjust for actual user
            ]);
            $res = [
                'sukses' => '1',
                'pesan' => 'Project added successfully!',
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
        $projectid = $this->request->getPost('id');
        $projectname = $this->request->getPost('projectname');
        $description = $this->request->getPost('description');
        $startdate = $this->request->getPost('startdate');
        $enddate = $this->request->getPost('enddate');
        $filepath = $this->request->getFile('filepath');
        $res = [];

        $this->db->transBegin();
        try {
            if (empty($projectname))
                throw new Exception("Project Name is required!");
            if (empty($description))
                throw new Exception("Description is required!");
            if (empty($startdate))
                throw new Exception("Start Date is required!");
            if (empty($enddate))
                throw new Exception("End Date is required!");
            // Ambil data produk lama untuk mendapatkan gambar sebelumnya
            $oldData = $this->projectModel->getOne($projectid);
            if (empty($oldData))
                throw new Exception("Product not found!");

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
                $filepath->move('upload/project', $newName);
                $newFilePath = 'upload/project/' . $newName;

                // Hapus file lama jika ada
                if (file_exists($oldData['filepath'])) {
                    unlink($oldData['filepath']);
                }
            }

            $data = [
                'projectname' => $projectname,
                'description' => $description,
                'startdate' => $startdate,
                'enddate' => $enddate,
                'filepath' => $newFilePath,
                'updateddate' => date('Y-m-d H:i:s'),
                'updatedby' => getSession('userid'), // Adjust for actual user
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
            if (empty($row))
                throw new Exception("Project not found!");

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

    public function exportexcel()
    {
        // Query all projects from the database
        $projects = $this->projectModel->findAll();

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set the header row
        $sheet->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Project Name')
            ->setCellValue('C1', 'Description')
            ->setCellValue('D1', 'Start Date')
            ->setCellValue('E1', 'End Date')
            ->setCellValue('F1', 'File Path');

        // Fill in the data
        $row = 2; // Starting row for project data
        foreach ($projects as $index => $project) {
            $sheet->setCellValue('A' . $row, $index + 1)
                ->setCellValue('B' . $row, $project['projectname'])
                ->setCellValue('C' . $row, $project['description'])
                ->setCellValue('D' . $row, $project['startdate'])
                ->setCellValue('E' . $row, $project['enddate'])
                ->setCellValue('F' . $row, $project['filepath']);
            $row++;
        }

        // Create writer and output the file
        $writer = new Xlsx($spreadsheet);

        // Set the file name for the download
        $fileName = 'projects.xlsx';

        // Send the appropriate headers to download the file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        // Write to output
        $writer->save('php://output');
    }

    public function generatePdf()
{
    // Include FPDF library
    $pdf = new Fpdf();

    // Add a page
    $pdf->AddPage();

    // Set font for the title
    $pdf->SetFont('Arial', 'B', 16);

    // Add a title
    $pdf->Cell(200, 10, 'Project Data', 0, 1, 'C');

    // Set font for body
    $pdf->SetFont('Arial', '', 12);

    // Create a table header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Project Name', 1, 0, 'C');
    $pdf->Cell(80, 10, 'Description', 1, 0, 'C');
    $pdf->Cell(35, 10, 'Start Date', 1, 0, 'C');
    $pdf->Cell(35, 10, 'End Date', 1, 1, 'C');

    // Retrieve project data from the model
    $projects = $this->projectModel->findAll();

    $pdf->SetFont('Arial', '', 12);

    foreach ($projects as $project) {
        // Calculate the height for the description field
        $startX = $pdf->GetX();
        $startY = $pdf->GetY();
        $cellWidth = 80; // Width of the description column
        $lineHeight = 6; // Height of each line in MultiCell

        // Save the current position and create a MultiCell for description
        $pdf->SetXY($startX + 40, $startY); // Move to the description column
        $pdf->MultiCell($cellWidth, $lineHeight, $project['description'], 1, 'L');

        // Get the height of the MultiCell
        $descriptionHeight = $pdf->GetY() - $startY;

        // Calculate the max height of the row
        $rowHeight = max($descriptionHeight, 10); // Ensure at least the default cell height

        // Reset cursor to draw the Project Name cell
        $pdf->SetXY($startX, $startY);
        $pdf->Cell(40, $rowHeight, $project['projectname'], 1, 0, 'C');

        // Move cursor to Start Date column
        $pdf->SetXY($startX + 120, $startY);
        $pdf->Cell(35, $rowHeight, $project['startdate'], 1, 0, 'C');

        // Move cursor to End Date column
        $pdf->SetXY($startX + 155, $startY);
        $pdf->Cell(35, $rowHeight, $project['enddate'], 1, 1, 'C');
    }

    // Output the PDF to the browser for download
    $pdf->Output('D', 'projects.pdf');
}

}
