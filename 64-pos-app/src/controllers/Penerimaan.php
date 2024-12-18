<?php
namespace MyApp\Controllers;

use Mpdf\Mpdf;
use MyApp\Core\BaseController;
use MyApp\Core\Message;
use MyApp\Models\PembelianModel;

class Penerimaan extends BaseController
{
  private $penerimaanModel;
  public function __construct()
  {
    $this->penerimaanModel = $this->model('MyApp\Models\PenerimaanModel');
  }

  public function index()
  {
    $data = [
      'judul' => 'Penerimaan Pembelian',
      'penerimaan' => $this->penerimaanModel->getAll()
    ];
    $this->view('template/header', $data);
    $this->view('penerimaan/index', $data);
    $this->view('template/footer');
  }

  public function insert()
  {
    $pembelianDtl = [];
    $pembelian_id = "";
    $pembelianModel = new PembelianModel();
    if (isset($_POST['pembelian'])) {
      $pembelianDtl = $pembelianModel->getPembelianDtl($_POST['pembelian']);
      $pembelian_id = $_POST['pembelian'];
    }
    $data = [
      'judul' => 'Tambah Data Penerimaan',
      'pembelianData' => $pembelianModel->getByStatus(0),
      'pembelianDtl' => $pembelianDtl,
      'pembelian_id' => $pembelian_id
    ];
    $this->view('template/header', $data);
    $this->view('penerimaan/insert', $data);
    $this->view('template/footer');
  }

  public function insert_data()
  {
    $pembelianDtl = [];
    $pembelian_id = "";
    $pembelianModel = new PembelianModel();
    if (isset($_POST['pembelian'])) {
      $pembelianDtl = $pembelianModel->getPembelianDtl($_POST['pembelian']);
      $pembelian_id = $_POST['pembelian'];
    }
    $arrayDtl = [];
    foreach ($pembelianDtl as $row) {
      $data = [
        "barang" => $row['id_barang'],
        "jumlah" => intval($_POST['barang'][$row['id_pembeliandtl']]),
        "keterangan" => strval($_POST['keterangan'][$row['id_pembeliandtl']])
      ];
      array_push($arrayDtl, $data);
    }
    $penerimaan = [
      "id_pembelian" => $pembelian_id,
      "tanggal" => date("Y-m-d"),
      "id_user" => 1,
      "keterangan" => strval($_POST['ket']),
      "detail" => $arrayDtl,
    ];

    $hasil = $this->penerimaanModel->insert($penerimaan);
    if ($hasil) {
      Message::setFlash('success', 'Berhasil !', "Data berhasil disimpan");
      $this->redirect('penerimaan');
    } else {
      Message::setFlash('danger', 'Gagal !', "Data gagal disimpan");
      $this->redirect('penerimaan/insert');
    }
  }

  public function printPenerimaan($id)
  {
    $penerimaan = $this->penerimaanModel->getById($id);
    $penerimaanDtl = $this->penerimaanModel->getDetailPenerimaan($id);
    $mpdf = new Mpdf();
    $data = [
      'detail' => 1,
      'penerimaan' => $penerimaan,
      'penerimaanDtl' => $penerimaanDtl
    ];
    $html = $this->view('penerimaan/print', $data, true);
    $mpdf->WriteHTML($html);
    $mpdf->Output('SlipPenerimaan.pdf', "I");
  }
}