<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: index.php");
    exit();
}

// Tambah data
if (isset($_POST['tambah'])) {
    $nama_kelas = $_POST['nama_kelas'];
    $kompetensi = $_POST['kompetensi_keahlian'];
    mysqli_query($conn, "INSERT INTO tabel_kelas (nama_kelas, kompetensi_keahlian) VALUES ('$nama_kelas', '$kompetensi')");
    echo "<script>alert('Data tersimpan'); window.location.href='kelas.php';</script>";
    exit();
}

// Edit data
if (isset($_POST['edit'])) {
    $id = $_POST['id_kelas'];
    $nama_kelas = $_POST['nama_kelas'];
    $kompetensi = $_POST['kompetensi_keahlian'];
    mysqli_query($conn, "UPDATE tabel_kelas SET nama_kelas='$nama_kelas', kompetensi_keahlian='$kompetensi' WHERE id_kelas=$id");
    echo "<script>alert('Data berhasil diupdate'); window.location.href='kelas.php';</script>";
    exit();
}

// Hapus data
if (isset($_GET['hapus'])) {
    mysqli_query($conn, "DELETE FROM tabel_kelas WHERE id_kelas=".$_GET['hapus']);
    echo "<script>alert('Data terhapus'); window.location.href='kelas.php';</script>";
    exit();
}

$data = mysqli_query($conn, "SELECT * FROM tabel_kelas");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kelas</title>
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
            <li class="active"><a href="kelas.php"><i class="fas fa-building"></i> Data Kelas</a></li>
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
        <div class="d-flex justify-content-between align-items-center mb-3"><h2><i class="fas fa-building"></i> Data Kelas</h2><button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="fas fa-plus"></i> Tambah Kelas</button></div>
        <div class="table-responsive"><table class="table table-bordered table-hover bg-white"><thead class="table-dark"><tr><th>No</th><th>Nama Kelas</th><th>Kompetensi Keahlian</th><th>Aksi</th></tr></thead><tbody>
            <?php $no=1; while($row=mysqli_fetch_assoc($data)): ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $row['nama_kelas']; ?></td>
                <td><?php echo $row['kompetensi_keahlian']; ?></td>
                <td>
                    <button class="btn btn-edit btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $row['id_kelas']; ?>"><i class="fas fa-edit"></i> Edit</button>
                    <a href="?hapus=<?php echo $row['id_kelas']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i> Hapus</a>
                </td>
            </tr>

            <!-- Modal Edit -->
            <div class="modal fade" id="modalEdit<?php echo $row['id_kelas']; ?>" tabindex="-1">
                <div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-warning text-dark"><h5>Edit Kelas</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form method="POST"><div class="modal-body">
                    <input type="hidden" name="id_kelas" value="<?php echo $row['id_kelas']; ?>">
                    <input type="text" name="nama_kelas" class="form-control mb-2" value="<?php echo $row['nama_kelas']; ?>" required>
                    <input type="text" name="kompetensi_keahlian" class="form-control mb-2" value="<?php echo $row['kompetensi_keahlian']; ?>" required>
                </div><div class="modal-footer"><button type="submit" name="edit" class="btn btn-warning">Update</button></div></form>
                </div></div>
            </div>
            <?php endwhile; ?>
        </tbody></table></div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-success text-white"><h5>Tambah Kelas</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form method="POST"><div class="modal-body"><input type="text" name="nama_kelas" class="form-control mb-2" placeholder="Nama Kelas" required><input type="text" name="kompetensi_keahlian" class="form-control mb-2" placeholder="Kompetensi Keahlian" required></div><div class="modal-footer"><button type="submit" name="tambah" class="btn btn-success">Simpan</button></div></form></div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>