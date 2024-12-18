<?php
require_once './template/header.php';
?>

<div class="container">
  <div class="row mt-3 justify-content-center">
    <nav class="col-10" aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a class="text-decoration-none" href="#">Home</a></li>
        <li class="breadcrumb-item"><a class="text-decoration-none" href="#">Master</a></li>
        <li class="breadcrumb-item active" aria-current="page">Input Mahasiswa</li>
      </ol>
    </nav>
    <div class="row justify-content-center">
      <div class="card col-10">
        <div class="card-body">
          <form id="form" action="./src/process/data.php" method="post">
            <div class="row mb-3">
              <label for="nama" class="col-2 col-form-label">Nama</label>
              <div class="col-5">
                <input type="text" id="nama" name="nama" class="form-control">
              </div>
            </div>
            <div class="row mb-3">
              <label for="tempat_lahir" class="col-2 col-form-label">Tempat, Tgl Lahir</label>
              <div class="col-3">
                <input type="text" id="tempat_lahir" name="tempat_lahir" class="form-control">
              </div>
              <div class="col-3">
                <input type="date" id="tgl_lahir" name="tgl_lahir" class="form-control">
              </div>
            </div>
            <div class="row mb-3">
              <label for="telepon" class="col-2 col-form-label">Telepon</label>
              <div class="col-3">
                <input type="text" id="telepon" name="telepon" class="form-control">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="telepon" class="col-sm-2 col-form-label">Jenis Kelamin</label>
              <div class="col-3">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="kelamin" id="laki_laki" value="1">
                  <label class="form-check-label" for="laki_laki">Laki-Laki</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="kelamin" id="perempuan" value="0">
                  <label class="form-check-label" for="perempuan">Perempuan</label>
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <label for="alamat" class="col-2 col-form-label">Alamat</label>
              <div class="col-5">
                <textarea name="alamat" id="alamat" class="form-control"></textarea>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-2"></div>
              <div class="col-5">
                <button onclick="insert()" type="button" class="btn btn-primary">Simpan</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
require_once './template/footer.php';
?>