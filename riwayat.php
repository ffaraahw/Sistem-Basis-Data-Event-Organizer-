<?php
session_start();
include 'koneksi.php';


if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM detail_transaksi WHERE id_transaksi='$id'");
    mysqli_query($conn, "DELETE FROM transaksi WHERE id_transaksi='$id'");
    header("Location: riwayat.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Pesanan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-dark bg-dark mb-4">
  <a class="navbar-brand" href="index.php">EO System</a>
  <a href="index.php" class="btn btn-secondary btn-sm">Kembali ke Menu</a>
</nav>

<div class="container">
    <h3>Riwayat Pesanan</h3>
    <table class="table table-bordered table-striped mt-3">
        <thead class="thead-dark">
            <tr>
                <th>No</th>
                <th>Paket Event</th>
                <th>Tgl Acara</th>
                <th>Total Harga</th>
                <th>Pembayaran</th>
                <th>Detail Vendor</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $id_user = $_SESSION['id_user'];
            
            $query = "SELECT t.*, p.nama_paket 
                      FROM transaksi t 
                      JOIN paket p ON t.id_paket = p.id_paket 
                      WHERE t.id_user = '$id_user' 
                      ORDER BY t.id_transaksi DESC";
            $result = mysqli_query($conn, $query);
            $no = 1;

            if(mysqli_num_rows($result) == 0) {
                echo "<tr><td colspan='7' class='text-center'>Belum ada riwayat pesanan.</td></tr>";
            }

            while ($row = mysqli_fetch_assoc($result)) {
                $id_transaksi = $row['id_transaksi'];
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><b><?= $row['nama_paket'] ?></b></td>
                <td><?= date('d M Y', strtotime($row['tgl_acara'])) ?></td>
                <td>Rp <?= number_format($row['total_harga']) ?></td>
                <td>
                    <span class="badge badge-info"><?= $row['metode_pembayaran'] ?></span><br>
                    <small class="text-muted"><?= $row['status_pembayaran'] ?></small>
                </td>
                <td>
                    <ul class="pl-3 mb-0 small">
                    <?php
                    $qDet = "SELECT v.nama_vendor, j.nama_jenis 
                             FROM detail_transaksi dt 
                             JOIN vendor v ON dt.id_vendor = v.id_vendor 
                             JOIN jenis_vendor j ON v.id_jenis = j.id_jenis
                             WHERE dt.id_transaksi = '$id_transaksi'";
                    $rDet = mysqli_query($conn, $qDet);
                    while($d = mysqli_fetch_assoc($rDet)){
                        echo "<li>{$d['nama_jenis']}: {$d['nama_vendor']}</li>";
                    }
                    ?>
                    </ul>
                </td>
                <td>
                    <a href="edit_pesanan.php?id=<?= $row['id_transaksi'] ?>" class="btn btn-warning btn-sm mb-1">Edit</a>
                    <a href="riwayat.php?hapus=<?= $row['id_transaksi'] ?>" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Yakin batalkan pesanan ini?')">Batal</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>