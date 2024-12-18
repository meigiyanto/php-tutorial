<?php
namespace MyApp\Models;

use MyApp\Core\Database;
use MyApp\Helpers\DocNumber;
use PDO;
use PDOException;

class PenerimaanModel extends Database
{
  public function __construct()
  {
    parent::__construct();
    $this->setTableName('penerimaan');
    $this->setColumn([
      'id_penerimaan',
      'kode_penerimaan',
      'id_pembelian',
      'tanggal',
      'id_user',
      'keterangan'
    ]);
  }

  public function getAll()
  {
    $sql = "
    SELECT p.id_penerimaan,b.kode_pembelian,b.tanggal as tgl_pembelian,p.tanggal tgl_penerimaan, 
    p.keterangan FROM `penerimaan` p inner join pembelian b on(p.id_pembelian=b.id_pembelian)
    ";
    return $this->qry($sql)->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getById($id)
  {
    return $this->get(['id_penerimaan' => $id])->fetch(PDO::FETCH_ASSOC);
  }

  public function getDetailPenerimaan($id)
  {
    $sql = "select
        pd.id_penerimaan,
        b.kode_barang,
        b.nama_barang,
        b.harga_beli,
        pd.jumlah,
        pd.keterangan
      from
        penerimaan_dtl pd
      inner join barang b on
        (pd.id_barang = b.id_barang)
      where
        pd.id_penerimaan = ?";
    return $this->qry($sql, [$id])->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insert($data)
  {
    try {
      $pdo = $this->setConnection();
      $pdo->beginTransaction();
      $documentModel = new DocNumber();
      $document = $documentModel->getData('ss');
      $sql = "insert into penerimaan
      (kode_penerimaan,id_pembelian,tanggal,id_user,keterangan)
      values(?, ?, ?, ?, ?)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        $document,
        $data['id_pembelian'],
        $data['tanggal'],
        $data['id_user'],
        $data['keterangan']
      ]);
      $id = $pdo->lastInsertId();
      foreach ($data['detail'] as $row) {
        $sql = "insert into penerimaan_dtl
        (id_penerimaan,id_barang,jumlah,keterangan)
        values(?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
          $id,
          $row['barang'],
          $row['jumlah'],
          $row['keterangan']
        ]);
        // tambah jumlah barang
        $sql = "update barang set jumlah = jumlah + ? where id_barang = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
          $row['jumlah'],
          $row['barang']
        ]);
        // ubah status pembelian jadi close
        $sql = "update pembelian set status = 1 where id_pembelian = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
          $data['id_pembelian']
        ]);
      }
      return $pdo->commit();
    } catch (PDOException $e) {
      $pdo->rollBack();
      echo $e->getMessage();
      return null;
    }
  }
}