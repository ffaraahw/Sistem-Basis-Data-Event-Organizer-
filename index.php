<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user'])) { header("Location: login.php"); }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Pilih Kategori</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">Event Organizer App</a>
  <div class="ml-auto text-white">
      Halo, <b><?= $_SESSION['nama'] ?></b> &nbsp;
      <a href="riwayat.php" class="btn btn-info btn-sm">Riwayat Pesanan</a>
      <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
  </div>
</nav>

<div class="container mt-4">
    <h3 class="mb-4">Langkah 1: Pilih Kategori Event Anda</h3>
    <div class="row">
        <?php
        
        $query = "SELECT * FROM kategori";
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_assoc($result)) {
        ?>
        <div class="col-md-4 mb-4">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h4 class="card-title"><?= $row['nama_kategori'] ?></h4>
                    <p class="card-text">Temukan paket terbaik untuk event <?= $row['nama_kategori'] ?> Anda.</p>
                    <a href="pilih_tema.php?id_kategori=<?= $row['id_kategori'] ?>" class="btn btn-primary mt-auto">Pilih Kategori</a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
</body>
</html>