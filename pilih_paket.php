<?php
session_start();
include 'koneksi.php';
$id_tema = $_GET['id_tema'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pilih Paket</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h3>Langkah 3: Pilih Paket</h3>
    <a href="javascript:history.back()" class="btn btn-secondary mb-3">Kembali</a>

    <div class="row">
        <?php
        $qPaket = "SELECT * FROM paket WHERE id_tema = '$id_tema'";
        $rPaket = mysqli_query($conn, $qPaket);
        
        if(mysqli_num_rows($rPaket) == 0){
             echo "<div class='col'><div class='alert alert-warning'>Paket belum tersedia untuk tema ini.</div></div>";
        }

        while($paket = mysqli_fetch_assoc($rPaket)) {
            $id_paket = $paket['id_paket'];
        ?>
        <div class="col-md-4">
            <div class="card mb-4 shadow">
                <div class="card-header bg-info text-white">
                    <h5><?= $paket['nama_paket'] ?></h5>
                </div>
                <div class="card-body">
                    <p><i><?= $paket['deskripsi_paket'] ?></i></p>
                    <hr>
                    <h6>Termasuk Vendor (Standar):</h6>
                    <ul class="list-group list-group-flush small mb-3">
                        <?php
                        
                        $qVendor = "SELECT v.nama_vendor, j.nama_jenis 
                                    FROM detail_paket dp 
                                    JOIN vendor v ON dp.id_vendor = v.id_vendor 
                                    JOIN jenis_vendor j ON v.id_jenis = j.id_jenis
                                    WHERE dp.id_paket = '$id_paket'";
                        $rVendor = mysqli_query($conn, $qVendor);
                        
                        while($v = mysqli_fetch_assoc($rVendor)){
                            echo "<li class='list-group-item px-0 py-1'><b>{$v['nama_jenis']}:</b> {$v['nama_vendor']}</li>";
                        }
                        ?>
                    </ul>
                    <a href="form_pesan.php?id_paket=<?= $id_paket ?>" class="btn btn-warning btn-block"><b>Pilih & Custom Vendor</b></a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
</body>
</html>