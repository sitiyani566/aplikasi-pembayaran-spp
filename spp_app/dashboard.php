<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: index.php");
    exit();
}

$total_siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tabel_siswa"))['total'];
$total_pembayaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tabel_pembayaran"))['total'];
$total_nominal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah_bayar) as total FROM tabel_pembayaran"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - SPP App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f0f2f5; }
        .sidebar { position: fixed; left: 0; top: 0; width: 280px; height: 100vh; background: linear-gradient(180deg, #1a2a6c 0%, #b21f1f 100%); color: white; z-index: 1000; overflow-y: auto; }
        .sidebar-header { padding: 25px 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.2); }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu li { margin: 5px 15px; }
        .sidebar-menu li a { display: flex; align-items: center; padding: 12px 20px; color: white; text-decoration: none; border-radius: 12px; transition: all 0.3s; }
        .sidebar-menu li a i { width: 25px; margin-right: 10px; }
        .sidebar-menu li a:hover { background: rgba(255,255,255,0.2); }
        .sidebar-menu li.active a { background: rgba(255,255,255,0.25); }
        .sidebar-logout { margin: 20px 15px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.2); }
        .sidebar-logout a { display: flex; align-items: center; padding: 12px 20px; color: white; text-decoration: none; border-radius: 12px; background: rgba(255,255,255,0.1); }
        .main-content { margin-left: 280px; padding: 20px; }
        .top-bar { background: white; border-radius: 15px; padding: 15px 25px; margin-bottom: 25px; display: flex; justify-content: flex-end; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .user-avatar { width: 45px; height: 45px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
        .stat-card { background: white; border-radius: 15px; padding: 20px; transition: all 0.3s; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .menu-card { background: white; border-radius: 15px; padding: 20px; text-align: center; transition: all 0.3s; text-decoration: none; display: block; }
        .menu-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        @media (max-width: 768px) { .sidebar { left: -280px; } .main-content { margin-left: 0; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><i class="fas fa-graduation-cap fa-3x"></i><h4>SPP Manager Pro</h4><p>Sistem Pembayaran SPP</p></div>
        <ul class="sidebar-menu">
            <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="siswa.php"><i class="fas fa-user-graduate"></i> Data Siswa</a></li>
            <li><a href="kelas.php"><i class="fas fa-building"></i> Data Kelas</a></li>
            <li><a href="spp.php"><i class="fas fa-money-bill-wave"></i> Data SPP</a></li>
            <li><a href="pembayaran.php"><i class="fas fa-credit-card"></i> Pembayaran</a></li>
            <li><a href="cek_pembayaran.php"><i class="fas fa-search"></i> Cek Pembayaran</a></li>
            <li><a href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a></li>
            <li><a href="petugas.php"><i class="fas fa-users"></i> Data Petugas</a></li>
        </ul>
        <div class="sidebar-logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    </div>

    <div class="main-content">
        <div class="top-bar"><div class="user-avatar"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></div></div>
        <div class="mb-4"><h2>Selamat Datang, <?php echo $_SESSION['username']; ?>!</h2><p class="text-muted">Kelola pembayaran SPP dengan mudah</p></div>
        <div class="row g-4 mb-5">
            <div class="col-md-4"><div class="stat-card"><i class="fas fa-user-graduate fa-2x" style="color: #4CAF50;"></i><div class="stat-number"><?php echo $total_siswa; ?></div><div class="stat-label">Total Siswa</div></div></div>
            <div class="col-md-4"><div class="stat-card"><i class="fas fa-credit-card fa-2x" style="color: #2196F3;"></i><div class="stat-number"><?php echo $total_pembayaran; ?></div><div class="stat-label">Total Transaksi</div></div></div>
            <div class="col-md-4"><div class="stat-card"><i class="fas fa-chart-line fa-2x" style="color: #FF9800;"></i><div class="stat-number">Rp <?php echo number_format($total_nominal, 0, ',', '.'); ?></div><div class="stat-label">Total Pendapatan</div></div></div>
        </div>
        <h4 class="mb-3">Menu Aplikasi</h4>
        <div class="row g-4">
            <div class="col-lg-3 col-md-4 col-sm-6"><a href="siswa.php" class="menu-card"><i class="fas fa-user-graduate fa-2x" style="color: #4CAF50;"></i><div class="menu-title">Data Siswa</div></a></div>
            <div class="col-lg-3 col-md-4 col-sm-6"><a href="kelas.php" class="menu-card"><i class="fas fa-building fa-2x" style="color: #2196F3;"></i><div class="menu-title">Data Kelas</div></a></div>
            <div class="col-lg-3 col-md-4 col-sm-6"><a href="spp.php" class="menu-card"><i class="fas fa-money-bill-wave fa-2x" style="color: #FF9800;"></i><div class="menu-title">Data SPP</div></a></div>
            <div class="col-lg-3 col-md-4 col-sm-6"><a href="pembayaran.php" class="menu-card"><i class="fas fa-credit-card fa-2x" style="color: #9C27B0;"></i><div class="menu-title">Pembayaran</div></a></div>
            <div class="col-lg-3 col-md-4 col-sm-6"><a href="cek_pembayaran.php" class="menu-card"><i class="fas fa-search fa-2x" style="color: #00BCD4;"></i><div class="menu-title">Cek Pembayaran</div></a></div>
            <div class="col-lg-3 col-md-4 col-sm-6"><a href="laporan.php" class="menu-card"><i class="fas fa-chart-line fa-2x" style="color: #E91E63;"></i><div class="menu-title">Laporan</div></a></div>
            <div class="col-lg-3 col-md-4 col-sm-6"><a href="petugas.php" class="menu-card"><i class="fas fa-users fa-2x" style="color: #dc3545;"></i><div class="menu-title">Data Petugas</div></a></div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>