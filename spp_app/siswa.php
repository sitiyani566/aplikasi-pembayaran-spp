<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: index.php");
    exit();
}

// Tambah data
if (isset($_POST['tambah'])) {
    $nisn = $_POST['nisn'];
    $nis = $_POST['nis'];
    $nama_siswa = $_POST['nama_siswa'];
    $id_kelas = $_POST['id_kelas'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];
    mysqli_query($conn, "INSERT INTO tabel_siswa (nisn, nis, nama_siswa, id_kelas, alamat, no_telp) VALUES ('$nisn', '$nis', '$nama_siswa', $id_kelas, '$alamat', '$no_telp')");
    echo "<script>alert('Data tersimpan'); window.location.href='siswa.php';</script>";
    exit();
}

// Edit data
if (isset($_POST['edit'])) {
    $id = $_POST['id_siswa'];
    $nisn = $_POST['nisn'];
    $nis = $_POST['nis'];
    $nama_siswa = $_POST['nama_siswa'];
    $id_kelas = $_POST['id_kelas'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];
    mysqli_query($conn, "UPDATE tabel_siswa SET nisn='$nisn', nis='$nis', nama_siswa='$nama_siswa', id_kelas=$id_kelas, alamat='$alamat', no_telp='$no_telp' WHERE id_siswa=$id");
    echo "<script>alert('Data berhasil diupdate'); window.location.href='siswa.php';</script>";
    exit();
}

// Hapus data
if (isset($_GET['hapus'])) {
    mysqli_query($conn, "DELETE FROM tabel_siswa WHERE id_siswa=".$_GET['hapus']);
    echo "<script>alert('Data terhapus'); window.location.href='siswa.php';</script>";
    exit();
}

$data = mysqli_query($conn, "SELECT s.*, k.nama_kelas FROM tabel_siswa s LEFT JOIN tabel_kelas k ON s.id_kelas = k.id_kelas");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Siswa</title>
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
            <li class="active"><a href="siswa.php"><i class="fas fa-user-graduate"></i> Data Siswa</a></li>
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
        <div class="d-flex justify-content-between align-items-center mb-3"><h2><i class="fas fa-user-graduate"></i> Data Siswa</h2><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="fas fa-plus"></i> Tambah Siswa</button></div>
        <div class="table-responsive"><table class="table table-bordered table-hover bg-white"><thead class="table-dark"><tr><th>No</th><th>NISN</th><th>NIS</th><th>Nama Siswa</th><th>Kelas</th><th>Alamat</th><th>No Telp</th><th>Aksi</th></tr></thead><tbody>
            <?php $no=1; while($row=mysqli_fetch_assoc($data)): ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $row['nisn']; ?></td>
                <td><?php echo $row['nis']; ?></td>
                <td><?php echo $row['nama_siswa']; ?></td>
                <td><?php echo $row['nama_kelas']; ?></td>
                <td><?php echo $row['alamat']; ?></td>
                <td><?php echo $row['no_telp']; ?></td>
                <td>
                    <button class="btn btn-edit btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $row['id_siswa']; ?>"><i class="fas fa-edit"></i> Edit</button>
                    <a href="?hapus=<?php echo $row['id_siswa']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i> Hapus</a>
                </td>
            </tr>

            <!-- Modal Edit -->
            <div class="modal fade" id="modalEdit<?php echo $row['id_siswa']; ?>" tabindex="-1">
                <div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-warning text-dark"><h5>Edit Siswa</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form method="POST"><div class="modal-body">
                    <input type="hidden" name="id_siswa" value="<?php echo $row['id_siswa']; ?>">
                    <input type="text" name="nisn" class="form-control mb-2" value="<?php echo $row['nisn']; ?>" required>
                    <input type="text" name="nis" class="form-control mb-2" value="<?php echo $row['nis']; ?>" required>
                    <input type="text" name="nama_siswa" class="form-control mb-2" value="<?php echo $row['nama_siswa']; ?>" required>
                    <select name="id_kelas" class="form-control mb-2" required>
                        <option value="">Pilih Kelas</option>
                        <?php $kelas = mysqli_query($conn, "SELECT * FROM tabel_kelas"); while($k=mysqli_fetch_assoc($kelas)): ?>
                        <option value="<?php echo $k['id_kelas']; ?>" <?php echo ($k['id_kelas']==$row['id_kelas'])?'selected':''; ?>><?php echo $k['nama_kelas']; ?></option>
                        <?php endwhile; ?>
                    </select>
                    <textarea name="alamat" class="form-control mb-2"><?php echo $row['alamat']; ?></textarea>
                    <input type="text" name="no_telp" class="form-control mb-2" value="<?php echo $row['no_telp']; ?>">
                </div><div class="modal-footer"><button type="submit" name="edit" class="btn btn-warning">Update</button></div></form>
                </div></div>
            </div>
            <?php endwhile; ?>
        </tbody></table></div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-primary text-white"><h5>Tambah Siswa</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form method="POST"><div class="modal-body"><input type="text" name="nisn" class="form-control mb-2" placeholder="NISN" required><input type="text" name="nis" class="form-control mb-2" placeholder="NIS" required><input type="text" name="nama_siswa" class="form-control mb-2" placeholder="Nama Siswa" required><select name="id_kelas" class="form-control mb-2" required><option value="">Pilih Kelas</option><?php $kelas2 = mysqli_query($conn, "SELECT * FROM tabel_kelas"); while($k=mysqli_fetch_assoc($kelas2)): ?><option value="<?php echo $k['id_kelas']; ?>"><?php echo $k['nama_kelas']; ?></option><?php endwhile; ?></select><textarea name="alamat" class="form-control mb-2" placeholder="Alamat"></textarea><input type="text" name="no_telp" class="form-control mb-2" placeholder="No Telepon"></div><div class="modal-footer"><button type="submit" name="tambah" class="btn btn-primary">Simpan</button></div></form></div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>