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
            // $row bisa berupa array atau object → samakan dulu
            $fileDir = is_array($row) ? ($row['filedirectory'] ?? '') : ($row->filedirectory ?? '');
            $fileId  = is_array($row) ? ($row['fileid'] ?? null)     : ($row->fileid ?? null);
            $realNm  = is_array($row) ? ($row['filerealname'] ?? '') : ($row->filerealname ?? '');
            $srvNm   = is_array($row) ? ($row['filename'] ?? '')     : ($row->filename ?? '');

            $isImage = false;
            if (is_string($fileDir) && $fileDir !== '' && is_file($fileDir)) {
                $mime = @mime_content_type($fileDir);
                if (is_string($mime) && preg_match('/^image\//', $mime)) {
                    $isImage = true;
                }
            }

            $btnPreview = $isImage
                ? "<button type='button' class='btn btn-sm btn-info' onclick=\"previewFile({$fileId})\"><i class='bx bx-show'></i></button>"
                : '';

            $btnEdit = "<button type='button' class='btn btn-sm btn-warning' onclick=\"modalForm('Edit File - " . esc($realNm) . "', 'modal-lg', '" . getURL('files/form/' . encrypting($fileId)) . "', {identifier: this})\"><i class='bx bx-edit-alt'></i></button>";

            $btnDownload = "<a href='" . getURL('files/download/' . $fileId) . "' class='btn btn-sm btn-success'><i class='bx bx-download'></i></a>";

            $btnDelete = "<button type='button' class='btn btn-sm btn-danger' onclick=\"modalDelete('Delete File - " . esc($realNm) . "', {'link':'" . getURL('files/delete') . "', 'id':'" . encrypting($fileId) . "', 'pagetype':'table'})\"><i class='bx bx-trash'></i></button>";

            $actions = "<div style='display:flex;gap:4px;justify-content:center;'>{$btnPreview}{$btnEdit}{$btnDownload}{$btnDelete}</div>";

            return [
                $no,
                esc($realNm),
                esc($srvNm),
                $actions,
            ];
        });

        $table->toJson();
    }

    public function upload()
    {
        $file = $this->request->getFile('file');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'sukses'    => 0,
                'pesan'     => 'File tidak valid',
                'csrfToken' => csrf_hash(),
            ]);
        }

        $uploadPath = WRITEPATH . 'uploads/files/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        return $this->response->setJSON([
            'sukses'       => 1,
            'pesan'        => 'File uploaded',
            'filename'     => $newName,
            'filedirectory' => $uploadPath . $newName,
            'originalname' => $file->getClientName(),
            'csrfToken'    => csrf_hash(),
        ]);
    }

    /**
     * Simpan data ke msfiles berdasarkan inputan modal (real name, server name, dll).
     */
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

                // kalau user tidak upload file baru, pakai data lama
                if (empty($serverName) || empty($fileDir)) {
                    $serverName = $before['filename']      ?? '';
                    $fileDir    = $before['filedirectory'] ?? '';
                }

                if (empty($realName)) {
                    $realName = $originalName ?: ($before['filerealname'] ?? $serverName);
                }

                if (empty($serverName) || empty($fileDir)) {
                    throw new Exception('Data file tidak lengkap untuk update');
                }

                $data = [
                    'filerealname'  => $realName,
                    'filename'      => $serverName,
                    'filedirectory' => $fileDir,
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

                $data = [
                    'filerealname'  => $realName,
                    'filename'      => $serverName,
                    'filedirectory' => $fileDir,
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
                'pesan'     => 'Data file berhasil disimpan',
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

            if (!empty($row['filedirectory']) && is_file($row['filedirectory'])) {
                @unlink($row['filedirectory']);
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
        if (!is_string($fileDir) || $fileDir === '' || !is_file($fileDir)) {
            return $this->response
                ->setStatusCode(404)
                ->setBody('File not found');
        }

        $path = $fileDir;

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
        if (!is_string($fileDir) || $fileDir === '' || !is_file($fileDir)) {
            return $this->response
                ->setStatusCode(404)
                ->setBody('File not found');
        }

        $path = $fileDir;
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
