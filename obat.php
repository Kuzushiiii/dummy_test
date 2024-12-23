<?= $this->extend('template/index'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid py-5">
  <div class="container">

    <div class="card shadow">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">

            <div class="btn-group">
              <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                Klik Untuk Pilih Aksi
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a href="<?= base_url('tambah') ?>" class="btn btn-primary dropdown-item" title="Tambah Data"><i class="fa-solid fa-plus"></i> Tambah Data</a></li>
                <li><a href="<?= base_url('obat/export') ?>" class="btn btn-warning dropdown-item"><i class="fa-solid fa-file-export"></i> Export ke Excel </a></li>
                <li><a href="<?= base_url('obat/import') ?>" class="btn btn-danger dropdown-item"><i class="fa-solid fa-file-import"></i> Import dari Excel </a></li>
              </ul>
            </div>




          </div>
          <div class="col-md-6">
            <form action="<?= base_url('obat/search') ?>" method="post">
              <div class="input-group mb-3">
                <input type="text" class="form-control" name="keyword" placeholder="Jelajahi Data Brdasarkan Nama dan Deskripsi">
                <a href="<?= base_url('obat') ?>" class="btn btn-outline-success"><i class="fa-solid fa-arrows-rotate"></i></a>
                <button class="btn btn-outline-success" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
              </div>
            </form>
          </div>
        </div>

        <table class="table table-hover">
          <thead>
            <tr>
              <th scope="col">No</th>
              <th scope="col">Nama Obat</th>
              <th scope="col">Deskripsi Obat</th>
              <th scope="col">Bentuk</th>
              <th scope="col">Asal Obat</th>
              <th scope="col">Harga Obat</th>
              <th scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1 + (6 * ($currentPage - 1)); ?>
            <?php foreach ($obat as $k) : ?>
              <tr>
                <th><?= $no++ ?></th>
                <td><?= $k['nama'] ?></td>
                <td><?= $k['deskripsi'] ?></td>
                <td><?= $k['bentuk'] ?></td>
                <td><?= $k['asal'] ?></td>
                <td>Rp. <?= $k['harga'] ?></td>
                <td>

                  <a href="<?= base_url('edit/' . $k['id_obat']) ?>" type="button" class="btn btn-warning" title="Edit Data"><i class="fa-solid fa-pen"></i></a>

                  <form action="<?= base_url('hapus/' . $k['id_obat']) ?>" method="post" class="d-inline" title="Hapus Data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda Yakin Hapus Data?')"><i class="fa-solid fa-trash"></i></button>
                  </form>

                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="row">
          <div class="col-md-6">
            <?= $pager->links('obat', 'obat_pagination'); ?>
          </div>

          <div class="col-md-6 text-end">
            <p>
              Menampilkan <?php echo 1 + (6 * ($currentPage - 1)) ?> Halaman dari <?php echo $no - 1 ?> data
            </p>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

<script>
  
</script>


<?= $this->endSection('content'); ?>