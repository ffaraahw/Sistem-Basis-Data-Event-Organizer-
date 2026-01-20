<?php
session_start();
include 'koneksi.php';
$id_paket = $_GET['id_paket'];


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


if (isset($_POST['pesan'])) {
    $id_user = $_SESSION['id_user'];
    $tgl_acara = $_POST['tgl_acara'];
    $metode_bayar = $_POST['metode_bayar']; 
    $tgl_pesan = date('Y-m-d');
    
    
    $qTrans = "INSERT INTO transaksi (id_user, id_paket, tgl_pemesanan, tgl_acara, total_harga, metode_pembayaran, status_pembayaran) 
               VALUES ('$id_user', '$id_paket', '$tgl_pesan', '$tgl_acara', 0, '$metode_bayar', 'Pending')";
    
    if(mysqli_query($conn, $qTrans)){
        $id_transaksi_baru = mysqli_insert_id($conn); 

        
        $total_harga = 0;
        foreach ($_POST['vendor_pilih'] as $id_vendor) {
            mysqli_query($conn, "INSERT INTO detail_transaksi (id_transaksi, id_vendor) VALUES ('$id_transaksi_baru', '$id_vendor')");
            
            $qHarga = mysqli_query($conn, "SELECT harga FROM vendor WHERE id_vendor='$id_vendor'");
            $dHarga = mysqli_fetch_assoc($qHarga);
            $total_harga += $dHarga['harga'];
        }

        
        mysqli_query($conn, "UPDATE transaksi SET total_harga='$total_harga' WHERE id_transaksi='$id_transaksi_baru'");

        echo "<script>alert('Pesanan Berhasil! Metode: $metode_bayar. Total: Rp " . number_format($total_harga) . "'); window.location='riwayat.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Pesanan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4 mb-5">
    <h3>Langkah 4: Kustomisasi & Checkout</h3>
    <a href="javascript:history.back()" class="btn btn-secondary mb-3">Kembali</a>

    <form method="POST">
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">Detail Pembayaran & Acara</div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Tanggal Acara</label>
                        <input type="date" name="tgl_acara" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Metode Pembayaran</label>
                        <select name="metode_bayar" class="form-control" required>
                            <option value="">- Pilih Metode -</option>
                            <option value="Transfer BCA">Transfer Bank BCA</option>
                            <option value="Transfer Mandiri">Transfer Bank Mandiri</option>
                            <option value="GoPay">E-Wallet (GoPay)</option>
                            <option value="OVO">E-Wallet (OVO)</option>
                            <option value="Kartu Kredit">Kartu Kredit (Visa/Mastercard)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <strong>Info Vendor:</strong> 
            <?php 
            if($kategori_diizinkan == "1, 2") {
                echo "Anda dapat memilih vendor dari kategori <b>Wedding & Lamaran</b>.";
            } else {
                echo "Anda hanya dapat memilih vendor khusus kategori ini.";
            }
            ?>
        </div>

        <?php
        
        $qDefault = "SELECT v.id_vendor, v.nama_vendor, v.harga, j.id_jenis, j.nama_jenis 
                     FROM detail_paket dp 
                     JOIN vendor v ON dp.id_vendor = v.id_vendor
                     JOIN jenis_vendor j ON v.id_jenis = j.id_jenis
                     WHERE dp.id_paket = '$id_paket'";
        $rDefault = mysqli_query($conn, $qDefault);

        while ($row = mysqli_fetch_assoc($rDefault)) {
            $jenis_id = $row['id_jenis'];
            $vendor_id_default = $row['id_vendor'];
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
                            $selected = ($v['id_vendor'] == $vendor_id_default) ? 'selected' : '';
                            echo "<option value='{$v['id_vendor']}' $selected>
                                    {$v['nama_vendor']} - Rp " . number_format($v['harga']) . "
                                  </option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        <?php } ?>

        <button type="submit" name="pesan" class="btn btn-success btn-lg btn-block mt-4">Konfirmasi & Bayar</button>
    </form>
</div>
</body>
</html>