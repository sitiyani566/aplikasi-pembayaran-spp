<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: index.php");
    exit();
}

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

$query = "SELECT p.*, s.nama_siswa, c.status_verifikasi 
          FROM tabel_pembayaran p 
          JOIN tabel_siswa s ON p.nisn = s.nisn 
          LEFT JOIN tabel_cek_pembayaran c ON p.id_pembayaran = c.id_pembayaran 
          WHERE 1=1";
if($bulan) $query .= " AND p.bulan_bayar='$bulan'";
if($tahun) $query .= " AND p.tahun_bayar=$tahun";
$query .= " ORDER BY p.tgl_bayar DESC";

$data = mysqli_query($conn, $query);
$total = 0;
$temp = mysqli_query($conn, $query);
while($row = mysqli_fetch_assoc($temp)) $total += $row['jumlah_bayar'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembayaran</title>
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
        .status-verified { background: #28a745; color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.7rem; }
        @media (max-width: 768px) { .sidebar { left: -280px; } .main-content { margin-left: 0; } }
        @media print { .sidebar, .top-bar, .btn, form, .no-print { display: none; } .main-content { margin-left: 0; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><i class="fas fa-graduation-cap fa-3x"></i><h4>SPP Manager Pro</h4><p>Sistem Pembayaran SPP</p></div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="siswa.php"><i class="fas fa-user-graduate"></i> Data Siswa</a></li>
            <li><a href="kelas.php"><i class="fas fa-building"></i> Data Kelas</a></li>
            <li><a href="spp.php"><i class="fas fa-money-bill-wave"></i> Data SPP</a></li>
            <li><a href="pembayaran.php"><i class="fas fa-credit-card"></i> Pembayaran</a></li>
            <li><a href="cek_pembayaran.php"><i class="fas fa-search"></i> Cek Pembayaran</a></li>
            <li class="active"><a href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a></li>
            <li><a href="petugas.php"><i class="fas fa-users"></i> Data Petugas</a></li>
        </ul>
        <div class="sidebar-logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    </div>

    <div class="main-content">
        <div class="top-bar"><div class="user-avatar"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></div></div>
        <h2><i class="fas fa-chart-line"></i> Laporan Pembayaran SPP</h2>

        <div class="card shadow mb-4 no-print">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4"><select name="bulan" class="form-control"><option value="">Semua Bulan</option><?php foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b) echo "<option ".($bulan==$b?'selected':'').">$b</option>"; ?></select></div>
                    <div class="col-md-3"><select name="tahun" class="form-control"><option value="">Semua Tahun</option><?php foreach(['2023','2024','2025','2026'] as $t) echo "<option ".($tahun==$t?'selected':'').">$t</option>"; ?></select></div>
                    <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Filter</button></div>
                    <div class="col-md-2"><a href="laporan.php" class="btn btn-secondary w-100">Reset</a></div>
                    <div class="col-md-1"><button onclick="window.print()" class="btn btn-success w-100"><i class="fas fa-print"></i></button></div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6"><div class="card bg-primary text-white"><div class="card-body"><h6>Total Transaksi</h6><h3><?php echo mysqli_num_rows($data); ?> Kali</h3></div></div></div>
            <div class="col-md-6"><div class="card bg-success text-white"><div class="card-body"><h6>Total Pendapatan</h6><h3>Rp <?php echo number_format($total,0,',','.'); ?></h3></div></div></div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-white">
                <thead class="table-dark"><tr><th>No</th><th>Siswa</th><th>Tgl Bayar</th><th>Bulan/Tahun</th><th>Jumlah</th><th>Status</th></tr></thead>
                <tbody>
                    <?php $no=1; $data = mysqli_query($conn, $query); while($row=mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['nama_siswa']; ?></td>
                        <td><?php echo $row['tgl_bayar']; ?></td>
                        <td><?php echo $row['bulan_bayar'] . ' ' . $row['tahun_bayar']; ?></td>
                        <td>Rp <?php echo number_format($row['jumlah_bayar'],0,',','.'); ?></td>
                        <td><span class="status-verified"><i class="fas fa-check-circle"></i> LUNAS</span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>