<?php
session_start();
include 'koneksi.php';
$id_kat = $_GET['id_kategori'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pilih Tema</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h3>Langkah 2: Pilih Tema</h3>
    <a href="index.php" class="btn btn-secondary mb-3">Kembali</a>
    
    <div class="row">
        <?php
        $query = "SELECT * FROM tema WHERE id_kategori = '$id_kat'";
        $result = mysqli_query($conn, $query);
        
        if(mysqli_num_rows($result) == 0){
            echo "<div class='col'><h5>Belum ada tema untuk kategori ini.</h5></div>";
        }

        while($row = mysqli_fetch_assoc($result)) {
        ?>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title font-weight-bold text-center"><?= $row['nama_tema'] ?></h5>
                    
                    <p class="card-text text-muted text-justify">
                        <?= $row['deskripsi_tema'] ?>
                    </p>
                    
                    <a href="pilih_paket.php?id_tema=<?= $row['id_tema'] ?>" class="btn btn-success mt-auto btn-block">Lihat Paket</a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
</body>
</html>