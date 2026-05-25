<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: index.php");
    exit();
}

// Tambah data
if (isset($_POST['tambah'])) {
    $tahun = $_POST['tahun_ajaran'];
    $nominal = $_POST['nominal'];
    $keterangan = $_POST['keterangan'];
    mysqli_query($conn, "INSERT INTO tabel_spp (tahun_ajaran, nominal, keterangan) VALUES ('$tahun', $nominal, '$keterangan')");
    echo "<script>alert('Data tersimpan'); window.location.href='spp.php';</script>";
    exit();
}

// Edit data
if (isset($_POST['edit'])) {
    $id = $_POST['id_spp'];
    $tahun = $_POST['tahun_ajaran'];
    $nominal = $_POST['nominal'];
    $keterangan = $_POST['keterangan'];
    mysqli_query($conn, "UPDATE tabel_spp SET tahun_ajaran='$tahun', nominal=$nominal, keterangan='$keterangan' WHERE id_spp=$id");
    echo "<script>alert('Data berhasil diupdate'); window.location.href='spp.php';</script>";
    exit();
}

// Hapus data
if (isset($_GET['hapus'])) {
    mysqli_query($conn, "DELETE FROM tabel_spp WHERE id_spp=".$_GET['hapus']);
    echo "<script>alert('Data terhapus'); window.location.href='spp.php';</script>";
    exit();
}

$data = mysqli_query($conn, "SELECT * FROM tabel_spp");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data SPP</title>
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
        .btn-edit { background: #ffc107; color: #333; }
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
            <li class="active"><a href="spp.php"><i class="fas fa-money-bill-wave"></i> Data SPP</a></li>
            <li><a href="pembayaran.php"><i class="fas fa-credit-card"></i> Pembayaran</a></li>
            <li><a href="cek_pembayaran.php"><i class="fas fa-search"></i> Cek Pembayaran</a></li>
            <li><a href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a></li>
            <li><a href="petugas.php"><i class="fas fa-users"></i> Data Petugas</a></li>
        </ul>
        <div class="sidebar-logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    </div>

    <div class="main-content">
        <div class="top-bar"><div class="user-avatar"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></div></div>
        <div class="d-flex justify-content-between align-items-center mb-3"><h2><i class="fas fa-money-bill-wave"></i> Data SPP</h2><button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="fas fa-plus"></i> Tambah SPP</button></div>
        <div class="table-responsive"><table class="table table-bordered table-hover bg-white"><thead class="table-dark"><tr><th>No</th><th>Tahun Ajaran</th><th>Nominal</th><th>Keterangan</th><th>Aksi</th></tr></thead><tbody>
            <?php $no=1; while($row=mysqli_fetch_assoc($data)): ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $row['tahun_ajaran']; ?></td>
                <td>Rp <?php echo number_format($row['nominal'],0,',','.'); ?></td>
                <td><?php echo $row['keterangan']; ?></td>
                <td>
                    <button class="btn btn-edit btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $row['id_spp']; ?>"><i class="fas fa-edit"></i> Edit</button>
                    <a href="?hapus=<?php echo $row['id_spp']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i> Hapus</a>
                </td>
            </tr>

            <!-- Modal Edit -->
            <div class="modal fade" id="modalEdit<?php echo $row['id_spp']; ?>" tabindex="-1">
                <div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-warning text-dark"><h5>Edit SPP</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form method="POST"><div class="modal-body">
                    <input type="hidden" name="id_spp" value="<?php echo $row['id_spp']; ?>">
                    <input type="text" name="tahun_ajaran" class="form-control mb-2" value="<?php echo $row['tahun_ajaran']; ?>" required>
                    <input type="number" name="nominal" class="form-control mb-2" value="<?php echo $row['nominal']; ?>" required>
                    <textarea name="keterangan" class="form-control mb-2"><?php echo $row['keterangan']; ?></textarea>
                </div><div class="modal-footer"><button type="submit" name="edit" class="btn btn-warning">Update</button></div></form>
                </div></div>
            </div>
            <?php endwhile; ?>
        </tbody></table></div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-warning text-dark"><h5>Tambah SPP</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form method="POST"><div class="modal-body"><input type="text" name="tahun_ajaran" class="form-control mb-2" placeholder="Tahun Ajaran" required><input type="number" name="nominal" class="form-control mb-2" placeholder="Nominal" required><textarea name="keterangan" class="form-control mb-2" placeholder="Keterangan"></textarea></div><div class="modal-footer"><button type="submit" name="tambah" class="btn btn-warning">Simpan</button></div></form></div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>