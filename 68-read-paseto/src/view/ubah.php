<?php
include '../getByid.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <h3>Edit barang</h3>
  <form action="../update.php" method="post">
    <table width="50%" border="0" cellpadding="3" cellspacing="0">
      <input type="hidden" name="id" value="<?= $data['barang_id'] ?>">
      <tr>
        <td width="30%">Nama Banrag</td>
        <td>:</td>
        <td>
          <input type="text" name="nama_barang" id="nama_barang" value="<?= $data['nama_barang'] ?>">
        </td>
      </tr>
      <tr>
        <td>jumlah</td>
        <td>:</td>
        <td>
          <input type="text" name="jumlah" id="jumlah" value="<?= $data['jumlah'] ?>">
        </td>
      </tr>
      <tr>
        <td>Harga</td>
        <td>:</td>
        <td>
          <input type="text" name="harga" id="harga" value="<?= $data['harga_satuan'] ?>">
        </td>
      </tr>
      <tr>
        <td>Kadaluarsa</td>
        <td>:</td>
        <td>
          <input type="date" name="kadaluarsa" id="kadaluarsa" value="<?= $data['expire_date'] ?>">
        </td>
      </tr>
      <tr>
        <td colspan="2"></td>
        <td><input type="submit" value="Simpan"></td>
      </tr>
    </table>
  </form>
</body>

</html>