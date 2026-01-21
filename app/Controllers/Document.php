<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\MDocument;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Fpdf\Fpdf;


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

            // Check for duplicate document name
            $existing = $this->MDocument->where('documentname', $documentname)->first();
            if ($existing) throw new Exception("Document name already exists!");

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
                'createdby' => getSession('userid'),
                'updateddate' => date('Y-m-d H:i:s'),
                'updatedby' => getSession('userid'),
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
    $db = db_connect();

    $db->transBegin();
    try {
        // Validasi input
        if (empty($description)) throw new Exception("Masukkan deskripsi");
        if (empty($documentname)) throw new Exception("Masukkan nama dokumen");

        // Ambil data dokumen lama berdasarkan user ID
        $oldDocument = $this->MDocument->getOne($userid);
        if (!$oldDocument) throw new Exception("Dokumen tidak ditemukan untuk ID tersebut");

        // Siapkan data untuk diperbarui
        $data = [
            'documentname' => $documentname,
            'description' => $description,
            'updateddate' => date('Y-m-d H:i:s'),
            'updatedby' => 1, // ID user yang melakukan pembaruan
        ];

        // Jika ada file baru yang diunggah
        if ($filepath && $filepath->isValid()) {
            $allowedExtensions = ['doc', 'docx', 'pdf', 'xlsx'];
            $extension = $filepath->getExtension();
            if (!in_array($extension, $allowedExtensions)) {
                throw new Exception("Format file tidak valid, hanya doc, docx, pdf, dan xlsx yang diperbolehkan!");
            }

            // Hapus file lama jika ada
            if (!empty($oldDocument['filepath']) && file_exists($oldDocument['filepath'])) {
                unlink($oldDocument['filepath']);
            }

            // Generate nama file unik untuk file baru
            $newName = $filepath->getRandomName();
            $filepath->move('uploads/document', $newName);
            $data['filepath'] = 'uploads/document/' . $newName; // Update file path di database
        }

        // Update data di database
        $result = $this->MDocument->edit($data, $userid);
        if (!$result) throw new Exception("Gagal memperbarui data dalam database!");

        $db->transCommit();

        $res = [
            'sukses' => '1',
            'pesan' => 'Sukses memperbarui dokumen',
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



    public function export()
    {
        // Fetch data from the model
        $documents = $this->MDocument->findAll();
    
        // Create a new Spreadsheet
        $spreadSheet = new Spreadsheet();
        $sheet = $spreadSheet->getActiveSheet();
    
        // Set the title at the top
        $sheet->setCellValue('A1', "Data Dokumen");
        $sheet->mergeCells('A1:D1'); // Merge sesuai kolom header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
        // Header columns (Row 3)
        $headers = ['No', 'Document Name', 'Description', 'File Path'];
        $columns = ['A', 'B', 'C', 'D'];
    
        foreach ($headers as $index => $header) {
            $sheet->setCellValue($columns[$index] . '3', $header);
        }
    
        // Style the header (Bold, Centered, Bottom Border)
        $sheet->getStyle('A3:D3')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);
    
        // Write data to the sheet starting from row 4
        $row = 4;
        foreach ($documents as $key => $document) {
            $sheet->setCellValue('A' . $row, $key + 1); // No
            $sheet->setCellValue('B' . $row, $document['documentname']); // Document Name
            $sheet->setCellValue('C' . $row, $document['description']); // Description
            $sheet->setCellValue('D' . $row, $document['filepath']); // File Path
    
            // Add row borders for better readability
            $sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    
            $row++;
        }
    
        // Adjust column widths for better readability
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    
        // Apply borders to the whole table
        $sheet->getStyle('A3:D' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);
    
        // Save file as Excel
        $writer = new Xlsx($spreadSheet);
        $filename = 'data_dokumen_' . date('Ymd_His') . '.xlsx';
    
        // Output the file to the browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
    
        $writer->save('php://output');
    }


    public function exportPDF()
    {
        // Fetch data from the model
        $documents = $this->MDocument->findAll();
    
        // Create a new instance of FPDF
        $pdf = new FPDF(); 
    
        // Add a page
        $pdf->AddPage();
    
        // Set the title and header format
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, "Data Dokumen", 0, 1, 'C');
        $pdf->Ln(10); // Line break
    
        // Table Header
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 8, 'No', 1, 0, 'C');
        $pdf->Cell(60, 8, 'Documentname', 1, 0, 'C');
        $pdf->Cell(60, 8, 'Description', 1, 0, 'C');
        $pdf->Cell(60, 8, 'FilePath', 1, 1, 'C'); // No "Actions" column
    
        // Table Data
        $pdf->SetFont('Arial', '', 10);
        $no = 1;
        foreach ($documents as $document) {
            $pdf->Cell(10, 8, $no++, 1, 0, 'C');
            $pdf->Cell(60, 8, $document['documentname'], 1, 0, 'C');
            $pdf->Cell(60, 8, $document['description'], 1, 0, 'C');
    
            // Shorten the FilePath if it's too long (e.g., max length 40 characters)
            $filePath = (strlen($document['filepath']) > 40) ? substr($document['filepath'], 0, 40) . '...' : $document['filepath'];
    
            $pdf->Cell(60, 8, $filePath, 1, 1, 'C'); // Display shortened file path
        }
    
        // Set the appropriate headers for PDF download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Data_Dokumen_' . date('Ymd_His') . '.pdf"');
        header('Cache-Control: max-age=0');
    
        // Output the PDF as a downloadable file
        $pdf->Output('D', 'Data_Dokumen_' . date('Ymd_His') . '.pdf');
    }

    public function formImport()
    {
        $dt['view'] = view('master/document/v_import', []);
        $dt['csrfToken'] = csrf_hash();
        echo json_encode($dt);
    }

    public function importExcel()
    {
        $datas = json_decode($this->request->getPost('datas'));
        $res = array();
        $this->db->transBegin();
        try {
            $undfhdocument = 0;
            $undfhdocumentarr = [];

            foreach ($datas as $dt) {
                if (
                    empty($dt[0]) || // documentname
                    empty($dt[1]) || // description
                    empty($dt[2])   // filepath
                ) {
                    $undfhdocument++;
                    $undfhdocumentarr[] = $dt[0] ?? '-';
                    continue;
                }

                $this->MDocument->store([
                    'documentname' => trim($dt[0]),
                    'description' => trim($dt[1]),
                    'filepath' => trim($dt[2]),
                    'createddate' => date('Y-m-d H:i:s'),
                    'createdby' => getSession('userid'),
                    'updateddate' => date('Y-m-d H:i:s'),
                    'updatedby' => getSession('userid'),
                ]);
            }

            $res = [
                'sukses' => '1',
                'undfhproduct' => $undfhdocument,
                'undfhproductarr' => $undfhdocumentarr
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
