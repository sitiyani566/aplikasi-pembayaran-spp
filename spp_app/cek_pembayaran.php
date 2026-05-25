<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: index.php");
    exit();
}

$hasil_pencarian = null;
$siswa_data = null;

if (isset($_POST['cari'])) {
    $nisn = $_POST['nisn'];
    $siswa_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT s.*, k.nama_kelas FROM tabel_siswa s LEFT JOIN tabel_kelas k ON s.id_kelas = k.id_kelas WHERE s.nisn = '$nisn'"));
    
    if ($siswa_data) {
        $pembayaran = mysqli_query($conn, "SELECT bulan_bayar, tahun_bayar FROM tabel_pembayaran p 
                                            LEFT JOIN tabel_cek_pembayaran c ON p.id_pembayaran = c.id_pembayaran 
                                            WHERE p.nisn = '$nisn' AND c.status_verifikasi = 'verified'");
        $sudah_bayar = [];
        while($row = mysqli_fetch_assoc($pembayaran)) {
            $sudah_bayar[] = $row['bulan_bayar'] . '_' . $row['tahun_bayar'];
        }
        
        $bulan_list = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $tahun_ajaran = date('Y');
        
        $hasil_pencarian = [];
        foreach($bulan_list as $bulan) {
            $key = $bulan . '_' . $tahun_ajaran;
            if (in_array($key, $sudah_bayar)) {
                $hasil_pencarian[] = ['bulan' => $bulan, 'tahun' => $tahun_ajaran, 'lunas' => true];
            } else {
                $hasil_pencarian[] = ['bulan' => $bulan, 'tahun' => $tahun_ajaran, 'lunas' => false];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cek Pembayaran - Aplikasi SPP</title>
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
        .status-lunas { background: #28a745; color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; display: inline-block; }
        .status-belum { background: #dc3545; color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; display: inline-block; }
        .info-siswa { background: linear-gradient(135deg, #667eea, #764ba2); color: white; border-radius: 15px; padding: 20px; margin-bottom: 20px; }
        @media (max-width: 768px) { .sidebar { left: -280px; } .main-content { margin-left: 0; } }
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
            <li class="active"><a href="cek_pembayaran.php"><i class="fas fa-search"></i> Cek Pembayaran</a></li>
            <li><a href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a></li>
            <li><a href="petugas.php"><i class="fas fa-users"></i> Data Petugas</a></li>
        </ul>
        <div class="sidebar-logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    </div>

    <div class="main-content">
        <div class="top-bar"><div class="user-avatar"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></div></div>
        
        <h2><i class="fas fa-search"></i> Cek Pembayaran SPP</h2>
        <p class="text-muted mb-4">Cek status pembayaran SPP siswa berdasarkan NISN</p>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-md-8">
                        <label>Masukkan NISN Siswa</label>
                        <input type="text" name="nisn" class="form-control" placeholder="Contoh: 1234567890" required>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" name="cari" class="btn btn-primary w-100"><i class="fas fa-search"></i> Cari Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (isset($_POST['cari']) && !$siswa_data): ?>
            <div class="alert alert-danger">Data siswa dengan NISN tersebut tidak ditemukan!</div>
        <?php endif; ?>

        <?php if ($siswa_data): ?>
            <div class="info-siswa">
                <div class="row">
                    <div class="col-md-8">
                        <h4><i class="fas fa-user-graduate"></i> <?php echo $siswa_data['nama_siswa']; ?></h4>
                        <p>NISN: <?php echo $siswa_data['nisn']; ?> | Kelas: <?php echo $siswa_data['nama_kelas']; ?></p>
                    </div>
                    <div class="col-md-4 text-end"><i class="fas fa-school fa-3x opacity-50"></i></div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header bg-primary text-white"><h5 class="mb-0">Status Pembayaran Tahun <?php echo date('Y'); ?></h5></div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-dark"><tr><th>No</th><th>Bulan</th><th>Tahun</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php $no=1; foreach($hasil_pencarian as $item): ?>
                            <tr><td><?php echo $no++; ?></td><td><?php echo $item['bulan']; ?></td><td><?php echo $item['tahun']; ?></td>
                            <td><?php if($item['lunas']): ?><span class="status-lunas">LUNAS</span><?php else: ?><span class="status-belum">BELUM LUNAS</span><?php endif; ?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php $total_bulan=count($hasil_pencarian); $bulan_lunas=0; foreach($hasil_pencarian as $item) if($item['lunas']) $bulan_lunas++; ?>
                    <div class="alert alert-info">Ringkasan: Lunas <?php echo $bulan_lunas; ?> dari <?php echo $total_bulan; ?> bulan (<?php echo round(($bulan_lunas/$total_bulan)*100); ?>%)</div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>