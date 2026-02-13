<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\MFiles;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class Files extends BaseController
{
    protected $model;
    protected $bc;
    protected $db;

    public function __construct()
    {
        helper('primary_helper');
        $this->model = new MFiles();
        $this->db    = db_connect();
        $this->bc = [
            ['Setting', 'Files']
        ];
    }

    public function index()
    {
        return view('master/files/v_list', [
            'title'      => 'Files',
            'breadcrumb' => $this->bc,
            'section'    => 'Setting Files',
        ]);
    }

    /**
     * Form modal add / edit file
     */
    public function form($id = '')
    {
        $form_type = (empty($id) ? 'add' : 'edit');
        $row = [];
        if ($id !== '') {
            $id  = decrypting($id);
            $row = $this->model->find($id) ?? [];
        }

        $dt['view'] = view('master/files/v_form', [
            'form_type' => $form_type,
            'row'       => $row,
            'fileid'    => $id,
        ]);
        $dt['csrfToken'] = csrf_hash();

        return $this->response->setJSON($dt);
    }

    public function datatable()
    {
        $table = Datatables::method([MFiles::class, 'datatable'], 'searchable')->make();

        $table->updateRow(function ($row, $no) {
            // Normalisasi row ke array
            if (is_object($row)) {
                $row = (array)$row;
            }

            $fileId   = $row['fileid']        ?? null;
            $realNm    = $row['filerealname']      ?? '';
            $srvNm     = $row['filename'] ?? '';
            $fileDir   = $row['filedirectory'] ?? '';
            $created   = $row['created_date']   ?? '';
            $createdById = $row['created_by'] ?? null;
            $createdBy = $row['created_by_name'] ?? $createdById;


            // Cek apakah file image untuk tombol preview
            $isImage = false;
            $fullPath = null;
            if (is_string($fileDir) && $fileDir !== '') {
                $fullPath = FCPATH . ltrim($fileDir, '/\\');
            }

            if ($fullPath && is_file($fullPath)) {
                $mime = @mime_content_type($fullPath);
                if (is_string($mime) && preg_match('/^image\//', $mime)) {
                    $isImage = true;
                }
            }

            $btnPreview = $isImage
                ? "<button type='button' class='btn btn-sm btn-info' onclick=\"previewFile({$fileId})\"><i class='bx bx-show'></i></button>"
                : '';

            // tombol edit metadata / ganti file
            $btnEdit = "<button type='button' class='btn btn-sm btn-warning' onclick=\"modalForm('Edit File', 'modal-lg', '" . getURL('files/form/' . encrypting($fileId)) . "', {identifier: this})\"><i class='bx bx-edit-alt'></i></button>";

            $btnDownload = "<a href='" . getURL('files/download/' . $fileId) . "' class='btn btn-sm btn-success'><i class='bx bx-download'></i></a>";

            $btnDelete = "<button type='button' class='btn btn-sm btn-danger' onclick=\"modalDelete('Delete File', {'link':'" . getURL('files/delete') . "', 'id':'" . encrypting($fileId) . "', 'pagetype':'table', 'after':'files'})\"><i class='bx bx-trash'></i></button>";

            $actions = "<div style='display:flex;gap:4px;justify-content:center;'>{$btnPreview}{$btnEdit}{$btnDownload}{$btnDelete}</div>";

            return [
                $no,
                esc($realNm !== '' ? $realNm : $srvNm),
                esc($fileDir),
                esc($created),
                esc($createdBy),
                $actions,
            ];
        });

        $table->toJson();
    }

    public function upload()
    {
        // METHOD LAMA (single upload) – boleh tetap dipakai kalau masih perlu
        $file = $this->request->getFile('file');
        $formType = $this->request->getPost('form_type');

        if (!$file) {
            return $this->response->setJSON([
                'sukses'    => 0,
                'pesan'     => 'Tidak ada file yang dikirim (field "file" kosong)',
                'csrfToken' => csrf_hash(),
            ]);
        }

        if (!$file->isValid()) {
            return $this->response->setJSON([
                'sukses'    => 0,
                'pesan'     => 'Upload error: ' . $file->getErrorString() . ' (' . $file->getError() . ')',
                'csrfToken' => csrf_hash(),
            ]);
        }

        $relativePath = 'uploads/files/';
        $uploadPath   = FCPATH . $relativePath;

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $newName      = $file->getRandomName();
        $originalName = $file->getClientName();

        $file->move($uploadPath, $newName);

        $createdBy = getSession('userid') ? (int) getSession('userid') : 9;

        if ($formType === 'add') {
            $data = [
                'filerealname'  => $originalName,
                'filename'      => $newName,
                'filedirectory' => $relativePath . $newName,
                'created_date'  => date('Y-m-d H:i:s'),
                'created_by'    => $createdBy,
            ];
            $this->model->insert($data);
        }

        return $this->response->setJSON([
            'sukses'        => 1,
            'pesan'         => 'File berhasil diupload',
            'filename'      => $newName,
            'filedirectory' => $relativePath . $newName,
            'originalname'  => $originalName,
            'csrfToken'     => csrf_hash(),
        ]);
    }

    /**
     * Chunk upload: terima potongan file dan gabung di server.
     * Param POST:
     * - uploadId (string)
     * - chunkIndex (int mulai 0)
     * - totalChunks (int)
     * - originalName (string)
     * - totalSize (int)
     * - form_type (add/edit)
     * - file (UploadedFile) => chunk data
     */
    public function chunkUpload()
    {
        $uploadId     = $this->request->getPost('uploadId');
        $chunkIndex   = (int) $this->request->getPost('chunkIndex');
        $totalChunks  = (int) $this->request->getPost('totalChunks');
        $originalName = $this->request->getPost('originalName');
        $totalSize    = (int) $this->request->getPost('totalSize');
        $formType     = $this->request->getPost('form_type');

        $file = $this->request->getFile('file');

        if (empty($uploadId) || !$file) {
            return $this->response->setJSON([
                'sukses'    => 0,
                'pesan'     => 'Param uploadId atau file kosong',
                'csrfToken' => csrf_hash(),
            ]);
        }

        // Validasi total size (misal max 100 MB)
        $maxSizeBytes = 100 * 1024 * 1024;
        if ($totalSize > $maxSizeBytes) {
            return $this->response->setJSON([
                'sukses'    => 0,
                'pesan'     => 'Ukuran file maksimal 100 MB',
                'csrfToken' => csrf_hash(),
            ]);
        }

        if (!$file->isValid()) {
            return $this->response->setJSON([
                'sukses'    => 0,
                'pesan'     => 'Upload error chunk: ' . $file->getErrorString(),
                'csrfToken' => csrf_hash(),
            ]);
        }

        // Simpan sementara ke writable/tmp
        $tmpDir = WRITEPATH . 'uploads/tmp/';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $tmpFilePath = $tmpDir . $uploadId . '.part';

        // Append chunk ke file .part
        $inputStream = fopen($file->getTempName(), 'rb');
        $targetMode  = file_exists($tmpFilePath) ? 'ab' : 'wb';
        $outputStream = fopen($tmpFilePath, $targetMode);

        if (!$inputStream || !$outputStream) {
            if ($inputStream) fclose($inputStream);
            if ($outputStream) fclose($outputStream);
            return $this->response->setJSON([
                'sukses'    => 0,
                'pesan'     => 'Gagal membuka stream untuk chunk',
                'csrfToken' => csrf_hash(),
            ]);
        }

        // copy manual supaya tidak pakai file_put_contents besar
        while (!feof($inputStream)) {
            $buffer = fread($inputStream, 8192);
            fwrite($outputStream, $buffer);
        }
        fclose($inputStream);
        fclose($outputStream);

        $isLastChunk = ($chunkIndex + 1 === $totalChunks);

        // Kalau masih ada chunk berikutnya, cukup balas sukses
        if (!$isLastChunk) {
            return $this->response->setJSON([
                'sukses'        => 1,
                'pesan'         => 'Chunk ' . $chunkIndex . ' tersimpan',
                'isLastChunk'   => false,
                'csrfToken'     => csrf_hash(),
            ]);
        }

        // LAST CHUNK: pindahkan ke folder final + insert DB (kalau ADD)
        if (!file_exists($tmpFilePath)) {
            return $this->response->setJSON([
                'sukses'    => 0,
                'pesan'     => 'File sementara tidak ditemukan saat finalisasi',
                'csrfToken' => csrf_hash(),
            ]);
        }

        // Simpan ke public/uploads/files
        $relativePath = 'uploads/files/';
        $uploadPath   = FCPATH . $relativePath;
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // nama random untuk disimpan di server
        $newName = uniqid('f_', true) . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $originalName);
        $finalPath = $uploadPath . $newName;

        // pindahkan / rename file part
        if (!rename($tmpFilePath, $finalPath)) {
            @unlink($tmpFilePath);
            return $this->response->setJSON([
                'sukses'    => 0,
                'pesan'     => 'Gagal memindahkan file final',
                'csrfToken' => csrf_hash(),
            ]);
        }

        $createdBy = getSession('userid') ? (int) getSession('userid') : 9;

        // Kalau ADD: langsung insert ke DB
        if ($formType === 'add') {
            $data = [
                'filerealname'  => $originalName,
                'filename'      => $newName,
                'filedirectory' => $relativePath . $newName,
                'created_date'  => date('Y-m-d H:i:s'),
                'created_by'    => $createdBy,
            ];
            $this->model->insert($data);
        }

        return $this->response->setJSON([
            'sukses'        => 1,
            'pesan'         => 'Upload selesai',
            'isLastChunk'   => true,
            'filename'      => $newName,
            'filedirectory' => $relativePath . $newName,
            'originalname'  => $originalName,
            'csrfToken'     => csrf_hash(),
        ]);
    }

    /**
     * Cancel upload: hapus file sementara (.part) berdasarkan uploadId
     */
    public function cancelUpload()
    {
        $uploadId = $this->request->getPost('uploadId');

        if (empty($uploadId)) {
            return $this->response->setJSON([
                'sukses'    => 0,
                'pesan'     => 'uploadId tidak dikirim',
                'csrfToken' => csrf_hash(),
            ]);
        }

        $tmpDir      = WRITEPATH . 'uploads/tmp/';
        $tmpFilePath = $tmpDir . $uploadId . '.part';

        if (is_file($tmpFilePath)) {
            @unlink($tmpFilePath);
        }

        return $this->response->setJSON([
            'sukses'    => 1,
            'pesan'     => 'Upload dibatalkan dan file sementara dihapus',
            'csrfToken' => csrf_hash(),
        ]);
    }

    public function save()
    {
        $this->db->transBegin();

        try {
            $fileId       = $this->request->getPost('fileid'); // kalau edit
            $realName     = $this->request->getPost('filerealname');
            $serverName   = $this->request->getPost('filename');
            $fileDir      = $this->request->getPost('filedirectory');
            $originalName = $this->request->getPost('originalname');

            if (!empty($fileId)) {
                // EDIT MODE
                $id     = decrypting($fileId);
                $before = $this->model->find($id) ?? [];

                $isNewFile = !empty($serverName) && !empty($fileDir);

                if (!$isNewFile) {
                    // tidak ada file baru, pakai data lama
                    $serverName = $before['filename']      ?? '';
                    $fileDir    = $before['filedirectory'] ?? '';
                }

                if ($isNewFile) {
                    if (!empty($originalName)) {
                        $realName = $originalName;
                    } else {
                        $realName = $serverName;
                    }
                } else {
                    if (empty($realName)) {
                        if (!empty($before['filerealname'])) {
                            $realName = $before['filerealname'];
                        } else {
                            $realName = $serverName;
                        }
                    }
                }
                if (empty($serverName) || empty($fileDir)) {
                    throw new Exception('Data file tidak lengkap untuk update');
                }

                // kalau ada file baru, hapus dulu file lama di disk
                if ($isNewFile && !empty($before['filedirectory'])) {
                    $oldPath = FCPATH . ltrim($before['filedirectory'], '/\\');
                    if (is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                // user yang meng-update
                $updatedBy = 9;
                if (getSession('userid')) {
                    $updatedBy = (int) getSession('userid');
                }

                $data = [
                    'filerealname'  => $realName,
                    'filename'      => $serverName,
                    'filedirectory' => $fileDir,
                    'update_date'   => date('Y-m-d H:i:s'),
                    'update_by'     => $updatedBy,
                ];

                $this->model->update($id, $data);
            } else {
                // ADD MODE
                if (empty($serverName) || empty($fileDir)) {
                    throw new Exception('Silakan upload file terlebih dahulu');
                }

                if (empty($realName)) {
                    $realName = $originalName ?: $serverName;
                }

                $createdBy = 9;
                if (getSession('userid')) {
                    $createdBy = (int) getSession('userid');
                }

                $data = [
                    'filerealname'  => $realName,
                    'filename'      => $serverName,
                    'filedirectory' => $fileDir,
                    'created_date'  => date('Y-m-d H:i:s'),
                    'created_by'    => $createdBy,
                ];

                $this->model->insert($data);
            }

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'sukses' => 0,
                    'pesan'  => 'Gagal menyimpan data file',
                ]);
            }

            $this->db->transCommit();
            return $this->response->setJSON([
                'sukses'    => 1,
                'pesan'     => 'File berhasil di update',
                'csrfToken' => csrf_hash(),
            ]);
        } catch (Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'sukses'    => 0,
                'pesan'     => $e->getMessage(),
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function delete()
    {
        $idEnc = $this->request->getPost('id');

        $this->db->transBegin();
        try {
            $id  = decrypting($idEnc);
            $row = $this->model->find($id);

            if (empty($row)) {
                throw new Exception('File tidak ditemukan');
            }

            if (!empty($row['filedirectory'])) {
                $path = FCPATH . ltrim($row['filedirectory'], '/\\');
                if (is_file($path)) {
                    @unlink($path);
                }
            }

            $this->model->delete($id);

            $this->db->transCommit();
            return $this->response->setJSON([
                'sukses'    => 1,
                'pesan'     => 'File berhasil dihapus',
                'csrfToken' => csrf_hash(),
            ]);
        } catch (Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'sukses'    => 0,
                'pesan'     => $e->getMessage(),
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function download($id)
    {
        $row = $this->model->find($id);

        // Normalisasi ke array
        if (is_object($row)) {
            $row = (array) $row;
        }

        $fileDir = $row['filedirectory'] ?? null;
        if (!is_string($fileDir) || $fileDir === '') {
            return $this->response
                ->setStatusCode(404)
                ->setBody('File not found (empty filedirectory)');
        }

        $path = FCPATH . ltrim($fileDir, '/\\');

        if (!is_file($path)) {
            // sementara, untuk debug, kirim path yang dicek
            return $this->response
                ->setStatusCode(404)
                ->setBody('File not found at: ' . $path);
        }

        // filerealname bisa null / tidak ada → fallback ke basename(path)
        $realName = isset($row['filerealname']) && is_string($row['filerealname'])
            ? $row['filerealname']
            : basename($path);

        // Pastikan nama file string
        $name = (string) $realName;

        $content = file_get_contents($path);
        if ($content === false) {
            return $this->response
                ->setStatusCode(500)
                ->setBody('Failed to read file');
        }

        return $this->response
            ->download($name, $content)
            ->setFileName($name);
    }

    public function preview($id)
    {
        $row = $this->model->find($id);

        if (is_object($row)) {
            $row = (array) $row;
        }

        $fileDir = $row['filedirectory'] ?? null;
        if (!is_string($fileDir) || $fileDir === '') {
            return $this->response
                ->setStatusCode(404)
                ->setBody('File not found (empty filedirectory)');
        }

        $path = FCPATH . ltrim($fileDir, '/\\');

        if (!is_file($path)) {
            return $this->response
                ->setStatusCode(404)
                ->setBody('File not found at: ' . $path);
        }
        $mime = mime_content_type($path);

        if (!is_string($mime) || !preg_match('/^image\//', $mime)) {
            return $this->response
                ->setStatusCode(400)
                ->setBody('Preview hanya untuk file gambar');
        }

        $content = file_get_contents($path);
        if ($content === false) {
            return $this->response
                ->setStatusCode(500)
                ->setBody('Failed to read file');
        }

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setBody($content);
    }
}
