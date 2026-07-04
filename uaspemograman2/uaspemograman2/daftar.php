<?php
require 'config.php';

// ----------------------------------------------------------------
// Fungsi: konversi tanggal ke nama hari (Bahasa Indonesia)
// ----------------------------------------------------------------
function hariIndo($tanggal) {
    $hari_array = ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"];
    return $hari_array[(int)date('w', strtotime($tanggal))];
}

// ----------------------------------------------------------------
// Fungsi: menjadwalkan hari/tanggal/jam kedatangan
// Aturan: kapasitas 1 hari maksimal 5 orang. Jika sudah penuh,
// otomatis dialihkan ke hari berikutnya (looping sampai ketemu
// hari yang masih tersedia slotnya).
// ----------------------------------------------------------------
function jadwalkan($conn, $tgl_mulai, $exclude_id = null) {
    $slots = ['08:00:00','09:00:00','10:00:00','11:00:00','13:00:00']; // 5 slot / hari
    $tanggal = $tgl_mulai;

    while (true) {
        $exWhere = $exclude_id ? " AND no_daftar != " . (int)$exclude_id : "";
        $tglSafe = mysqli_real_escape_string($conn, $tanggal);
        $res = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM tb_daftar WHERE tanggal_datang='$tglSafe'" . $exWhere);
        $row = mysqli_fetch_assoc($res);
        $jml = (int)$row['jml'];

        if ($jml < count($slots)) {
            return [hariIndo($tanggal), $tanggal, $slots[$jml]];
        }
        // hari ini penuh (5 orang) -> pindah ke hari berikutnya
        $tanggal = date('Y-m-d', strtotime($tanggal . ' +1 day'));
    }
}

// ----------------------------------------------------------------
// Hapus data
// ----------------------------------------------------------------
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM tb_daftar WHERE no_daftar=$id");
    header("Location: daftar.php");
    exit;
}

// ----------------------------------------------------------------
// Simpan (tambah / ubah)
// ----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_pemohon']));
    $tgl_daftar = mysqli_real_escape_string($conn, $_POST['tgl_daftar']);
    $edit_id = !empty($_POST['no_daftar_edit']) ? (int)$_POST['no_daftar_edit'] : null;

    if ($nama !== '' && $tgl_daftar !== '') {
        list($hari, $tanggal_datang, $jam) = jadwalkan($conn, $tgl_daftar, $edit_id);

        if ($edit_id) {
            mysqli_query($conn, "UPDATE tb_daftar SET
                nama_pemohon='$nama',
                tgl_daftar='$tgl_daftar',
                hari='$hari',
                tanggal_datang='$tanggal_datang',
                jam='$jam'
                WHERE no_daftar=$edit_id");
        } else {
            mysqli_query($conn, "INSERT INTO tb_daftar
                (nama_pemohon, tgl_daftar, hari, tanggal_datang, jam)
                VALUES ('$nama','$tgl_daftar','$hari','$tanggal_datang','$jam')");
        }
    }
    header("Location: daftar.php");
    exit;
}

// ----------------------------------------------------------------
// Ambil data untuk mode edit
// ----------------------------------------------------------------
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM tb_daftar WHERE no_daftar=$id");
    $edit_data = mysqli_fetch_assoc($res);
}

$data = mysqli_query($conn, "SELECT * FROM tb_daftar ORDER BY no_daftar DESC");

$active = 'daftar';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar - Pengajuan Paspor</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">

    <div class="info-box">
        <b>Aturan Penjadwalan:</b> Kapasitas 1 hari maksimal 5 orang. Jika tanggal daftar yang dipilih sudah penuh,
        sistem otomatis menjadwalkan kedatangan ke hari berikutnya secara otomatis, lengkap dengan hari &amp; jam.
    </div>

    <div class="card">
        <h2><?php echo $edit_data ? 'Ubah Data Pendaftaran' : 'Input Pendaftaran'; ?></h2>
        <form method="POST" action="daftar.php">
            <?php if ($edit_data): ?>
                <input type="hidden" name="no_daftar_edit" value="<?php echo $edit_data['no_daftar']; ?>">
                <div class="form-row">
                    <label>No. Daftar</label>
                    <input type="text" value="<?php echo $edit_data['no_daftar']; ?>" readonly>
                </div>
            <?php else: ?>
                <div class="form-row">
                    <label>No. Daftar</label>
                    <input type="text" value="Otomatis (Auto Increment)" readonly>
                </div>
            <?php endif; ?>
            <div class="form-row">
                <label>Nama Pemohon</label>
                <input type="text" name="nama_pemohon" required
                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['nama_pemohon']) : ''; ?>">
            </div>
            <div class="form-row">
                <label>Tanggal Daftar</label>
                <input type="date" name="tgl_daftar" required
                       value="<?php echo $edit_data ? $edit_data['tgl_daftar'] : date('Y-m-d'); ?>">
            </div>
            <button type="submit" class="btn"><?php echo $edit_data ? 'Update' : 'Simpan'; ?></button>
            <?php if ($edit_data): ?>
                <a href="daftar.php" class="btn secondary" style="text-decoration:none;display:inline-block;">Batal</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h2>Data Pendaftar</h2>
        <table>
            <tr>
                <th>No. Daftar</th>
                <th>Nama Pemohon</th>
                <th>Tgl Daftar</th>
                <th>Hari</th>
                <th>Tanggal Datang</th>
                <th>Jam</th>
                <th>Action</th>
            </tr>
            <?php if (mysqli_num_rows($data) == 0): ?>
                <tr><td colspan="7" class="empty">Belum ada data pendaftar</td></tr>
            <?php else: while ($r = mysqli_fetch_assoc($data)): ?>
                <tr>
                    <td><?php echo $r['no_daftar']; ?></td>
                    <td><?php echo htmlspecialchars($r['nama_pemohon']); ?></td>
                    <td><?php echo $r['tgl_daftar']; ?></td>
                    <td><?php echo $r['hari']; ?></td>
                    <td><?php echo $r['tanggal_datang']; ?></td>
                    <td><?php echo substr($r['jam'],0,5); ?></td>
                    <td class="action">
                        <a class="edit" href="daftar.php?edit=<?php echo $r['no_daftar']; ?>">edit</a>
                        <a class="hapus" href="daftar.php?hapus=<?php echo $r['no_daftar']; ?>"
                           onclick="return confirm('Hapus data ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; endif; ?>
        </table>
    </div>

</div>
</body>
</html>
