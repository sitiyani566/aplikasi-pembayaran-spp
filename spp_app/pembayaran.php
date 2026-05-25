<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['simpan'])) {
    $id_petugas = $_SESSION['id_petugas'];
    $nisn = $_POST['nisn'];
    $tgl_bayar = $_POST['tgl_bayar'];
    $bulan_bayar = $_POST['bulan_bayar'];
    $tahun_bayar = $_POST['tahun_bayar'];
    $id_spp = $_POST['id_spp'];
    $jumlah_bayar = $_POST['jumlah_bayar'];
    
    // Insert ke tabel pembayaran
    mysqli_query($conn, "INSERT INTO tabel_pembayaran (id_petugas, nisn, tgl_bayar, bulan_bayar, tahun_bayar, id_spp, jumlah_bayar) 
                         VALUES ($id_petugas, '$nisn', '$tgl_bayar', '$bulan_bayar', $tahun_bayar, $id_spp, $jumlah_bayar)");
    $id_pembayaran = mysqli_insert_id($conn);
    
    // LANGSUNG VERIFIED (tidak perlu pending)
    mysqli_query($conn, "INSERT INTO tabel_cek_pembayaran (id_pembayaran, status_verifikasi, tgl_verifikasi, catatan) 
                         VALUES ($id_pembayaran, 'verified', CURDATE(), 'Pembayaran valid - otomatis terverifikasi')");
    
    echo "<script>alert('Pembayaran berhasil dicatat dan LANGSUNG TERVERIFIKASI!'); window.location.href='pembayaran.php';</script>";
    exit();
}

$siswa = mysqli_query($conn, "SELECT * FROM tabel_siswa");
$data_spp = mysqli_query($conn, "SELECT * FROM tabel_spp");
$pembayaran = mysqli_query($conn, "SELECT p.*, s.nama_siswa, c.status_verifikasi 
                                    FROM tabel_pembayaran p 
                                    JOIN tabel_siswa s ON p.nisn = s.nisn 
                                    LEFT JOIN tabel_cek_pembayaran c ON p.id_pembayaran = c.id_pembayaran 
                                    ORDER BY p.id_pembayaran DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran SPP</title>
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
            <li class="active"><a href="pembayaran.php"><i class="fas fa-credit-card"></i> Pembayaran</a></li>
            <li><a href="cek_pembayaran.php"><i class="fas fa-search"></i> Cek Pembayaran</a></li>
            <li><a href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a></li>
            <li><a href="petugas.php"><i class="fas fa-users"></i> Data Petugas</a></li>
        </ul>
        <div class="sidebar-logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    </div>

    <div class="main-content">
        <div class="top-bar"><div class="user-avatar"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></div></div>
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="fas fa-credit-card"></i> Pembayaran SPP</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBayar">
                <i class="fas fa-plus"></i> Bayar SPP
            </button>
        </div>

        <!-- Tabel Riwayat Pembayaran -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-history"></i> Riwayat Pembayaran</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Siswa</th>
                                <th>Tgl Bayar</th>
                                <th>Bulan/Tahun</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while($row = mysqli_fetch_assoc($pembayaran)): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $row['nama_siswa']; ?></td>
                                <td><?php echo $row['tgl_bayar']; ?></td>
                                <td><?php echo $row['bulan_bayar'] . ' ' . $row['tahun_bayar']; ?></td>
                                <td>Rp <?php echo number_format($row['jumlah_bayar'], 0, ',', '.'); ?></td>
                                <td>
                                    <span class="status-verified">
                                        <i class="fas fa-check-circle"></i> VERIFIED
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            
                            <?php if (mysqli_num_rows($pembayaran) == 0): ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data pembayaran</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Bayar -->
    <div class="modal fade" id="modalBayar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5><i class="fas fa-credit-card"></i> Form Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Pilih Siswa</label>
                            <select name="nisn" class="form-control" required>
                                <option value="">-- Pilih Siswa --</option>
                                <?php while($s = mysqli_fetch_assoc($siswa)): ?>
                                    <option value="<?php echo $s['nisn']; ?>">
                                        <?php echo $s['nama_siswa']; ?> (<?php echo $s['nisn']; ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Tanggal Bayar</label>
                            <input type="date" name="tgl_bayar" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-2">
                            <label>Bulan Bayar</label>
                            <select name="bulan_bayar" class="form-control" required>
                                <option value="">Pilih Bulan</option>
                                <option>Januari</option><option>Februari</option><option>Maret</option><option>April</option>
                                <option>Mei</option><option>Juni</option><option>Juli</option><option>Agustus</option>
                                <option>September</option><option>Oktober</option><option>November</option><option>Desember</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Tahun Bayar</label>
                            <select name="tahun_bayar" class="form-control" required>
                                <option value="">Pilih Tahun</option>
                                <option>2023</option><option>2024</option><option>2025</option><option>2026</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Pilih SPP</label>
                            <select name="id_spp" class="form-control" required>
                                <option value="">-- Pilih SPP --</option>
                                <?php while($sp = mysqli_fetch_assoc($data_spp)): ?>
                                    <option value="<?php echo $sp['id_spp']; ?>">
                                        <?php echo $sp['tahun_ajaran']; ?> - Rp <?php echo number_format($sp['nominal'], 0, ',', '.'); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Jumlah Bayar</label>
                            <input type="number" name="jumlah_bayar" class="form-control" placeholder="Jumlah Bayar" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="simpan" class="btn btn-primary">Simpan Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>