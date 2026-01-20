<?php
session_start();
include 'koneksi.php';

$id_transaksi = $_GET['id'];


$qTrans = "SELECT * FROM transaksi WHERE id_transaksi = '$id_transaksi'";
$rTrans = mysqli_query($conn, $qTrans);
$dTrans = mysqli_fetch_assoc($rTrans);
$id_paket = $dTrans['id_paket']; 

$qCekKategori = "SELECT t.id_kategori 
                 FROM paket p 
                 JOIN tema t ON p.id_tema = t.id_tema 
                 WHERE p.id_paket = '$id_paket'";
$rCek = mysqli_query($conn, $qCekKategori);
$dCek = mysqli_fetch_assoc($rCek);
$id_kategori_sekarang = $dCek['id_kategori'];

if ($id_kategori_sekarang == 1 || $id_kategori_sekarang == 2) {
    $kategori_diizinkan = "1, 2"; 
} else {
    $kategori_diizinkan = "$id_kategori_sekarang";
}


$vendor_lama = [];
$qOldVendor = "SELECT id_vendor FROM detail_transaksi WHERE id_transaksi = '$id_transaksi'";
$rOldVendor = mysqli_query($conn, $qOldVendor);
while($row = mysqli_fetch_assoc($rOldVendor)){
    $vendor_lama[] = $row['id_vendor'];
}

if (isset($_POST['update'])) {
    $tgl_acara = $_POST['tgl_acara'];
    $metode_bayar = $_POST['metode_bayar'];

   
    $qUpdate = "UPDATE transaksi SET 
                tgl_acara = '$tgl_acara',
                metode_pembayaran = '$metode_bayar'
                WHERE id_transaksi = '$id_transaksi'";
    mysqli_query($conn, $qUpdate);

    
    mysqli_query($conn, "DELETE FROM detail_transaksi WHERE id_transaksi = '$id_transaksi'");

   
    $total_harga = 0;
    foreach ($_POST['vendor_pilih'] as $id_vendor) {
        mysqli_query($conn, "INSERT INTO detail_transaksi (id_transaksi, id_vendor) VALUES ('$id_transaksi', '$id_vendor')");
        
        $qHarga = mysqli_query($conn, "SELECT harga FROM vendor WHERE id_vendor='$id_vendor'");
        $dHarga = mysqli_fetch_assoc($qHarga);
        $total_harga += $dHarga['harga'];
    }

    
    mysqli_query($conn, "UPDATE transaksi SET total_harga='$total_harga' WHERE id_transaksi='$id_transaksi'");

    echo "<script>alert('Data Pesanan Berhasil Diperbarui! Total Baru: Rp " . number_format($total_harga) . "'); window.location='riwayat.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Pesanan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4 mb-5">
    <h3>Edit Pesanan</h3>
    <a href="riwayat.php" class="btn btn-secondary mb-3">Batal & Kembali</a>

    <form method="POST">
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-warning text-dark">Perbarui Detail Acara</div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Tanggal Acara</label>
                        <input type="date" name="tgl_acara" class="form-control" value="<?= $dTrans['tgl_acara'] ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Metode Pembayaran</label>
                        <select name="metode_bayar" class="form-control" required>
                            <option value="">- Pilih Metode -</option>
                            <?php
                            $metode = ["Transfer BCA", "Transfer Mandiri", "GoPay", "OVO", "Kartu Kredit"];
                            foreach($metode as $m){
                              
                                $cek = ($m == $dTrans['metode_pembayaran']) ? 'selected' : '';
                                echo "<option value='$m' $cek>$m</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">Edit Vendor</div>
            <div class="card-body">
                <?php
            
                $qDefault = "SELECT v.id_vendor, v.nama_vendor, v.harga, j.id_jenis, j.nama_jenis 
                             FROM detail_paket dp 
                             JOIN vendor v ON dp.id_vendor = v.id_vendor
                             JOIN jenis_vendor j ON v.id_jenis = j.id_jenis
                             WHERE dp.id_paket = '$id_paket'";
                $rDefault = mysqli_query($conn, $qDefault);

                while ($row = mysqli_fetch_assoc($rDefault)) {
                    $jenis_id = $row['id_jenis'];
                ?>
                    <div class="form-group row border-bottom pb-2">
                        <label class="col-sm-3 col-form-label font-weight-bold"><?= $row['nama_jenis'] ?></label>
                        <div class="col-sm-9">
                            <select name="vendor_pilih[]" class="form-control">
                                <?php
                                
                                $qFilter = "SELECT DISTINCT v.id_vendor, v.nama_vendor, v.harga 
                                            FROM vendor v
                                            JOIN detail_paket dp ON v.id_vendor = dp.id_vendor
                                            JOIN paket p ON dp.id_paket = p.id_paket
                                            JOIN tema t ON p.id_tema = t.id_tema
                                            WHERE v.id_jenis = '$jenis_id' 
                                            AND t.id_kategori IN ($kategori_diizinkan)";
                                
                                $rFilter = mysqli_query($conn, $qFilter);
                                while ($v = mysqli_fetch_assoc($rFilter)) {
                                    
                                    $selected = in_array($v['id_vendor'], $vendor_lama) ? 'selected' : '';
                                    
                                    echo "<option value='{$v['id_vendor']}' $selected>
                                            {$v['nama_vendor']} - Rp " . number_format($v['harga']) . "
                                          </option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <button type="submit" name="update" class="btn btn-warning btn-lg btn-block mt-4">Simpan Perubahan</button>
    </form>
</div>
</body>
</html>