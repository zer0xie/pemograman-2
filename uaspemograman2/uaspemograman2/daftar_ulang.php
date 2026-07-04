<?php
require 'config.php';

$daftar_list = mysqli_query($conn, "SELECT no_daftar, nama_pemohon, hari, tanggal_datang FROM tb_daftar ORDER BY no_daftar DESC");

// ----------------------------------------------------------------
// Hapus data
// ----------------------------------------------------------------
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM tb_daftar_ulang WHERE id=$id");
    header("Location: daftar_ulang.php");
    exit;
}

// ----------------------------------------------------------------
// Simpan (tambah / ubah)
// Logika:
// - KTP, KK, Ijazah/Akte = Ada / Tidak (dari checkbox)
// - Hari & Tanggal Datang (input) dibandingkan dengan jadwal
//   (hari, tanggal_datang) pada tb_daftar untuk No. Daftar terkait.
//   Jika sesuai -> Keterangan = OK, jika tidak -> Keterangan = Tidak
// - Jika Keterangan = OK, maka mendapat No. Antrian otomatis
// ----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_daftar   = (int)$_POST['no_daftar'];
    $keperluan   = mysqli_real_escape_string($conn, $_POST['keperluan']);
    $hari_datang = mysqli_real_escape_string($conn, $_POST['hari_datang']);
    $tgl_datang  = mysqli_real_escape_string($conn, $_POST['tgl_datang']);
    $ktp    = isset($_POST['ktp']) ? 'Ada' : 'Tidak';
    $kk     = isset($_POST['kk']) ? 'Ada' : 'Tidak';
    $ijazah = isset($_POST['ijazah']) ? 'Ada' : 'Tidak';
    $edit_id = !empty($_POST['id_edit']) ? (int)$_POST['id_edit'] : null;

    // ambil jadwal asli dari tb_daftar
    $res = mysqli_query($conn, "SELECT nama_pemohon, hari, tanggal_datang FROM tb_daftar WHERE no_daftar=$no_daftar");
    $jadwal = mysqli_fetch_assoc($res);

    if ($jadwal) {
        $nama_pemohon = mysqli_real_escape_string($conn, $jadwal['nama_pemohon']);

        if ($jadwal['hari'] === $hari_datang && $jadwal['tanggal_datang'] === $tgl_datang) {
            $keterangan = 'OK';
        } else {
            $keterangan = 'Tidak';
        }

        if ($edit_id) {
            // saat edit, no antrian dipertahankan jika sudah OK sebelumnya,
            // atau diberi baru jika status berubah jadi OK
            $resOld = mysqli_query($conn, "SELECT no_antrian FROM tb_daftar_ulang WHERE id=$edit_id");
            $old = mysqli_fetch_assoc($resOld);

            if ($keterangan === 'OK') {
                if (!empty($old['no_antrian'])) {
                    $no_antrian = (int)$old['no_antrian'];
                } else {
                    $resA = mysqli_query($conn, "SELECT MAX(no_antrian) AS mx FROM tb_daftar_ulang");
                    $rowA = mysqli_fetch_assoc($resA);
                    $no_antrian = ((int)$rowA['mx']) + 1;
                }
                $noAntrianSql = $no_antrian;
            } else {
                $noAntrianSql = "NULL";
            }

            mysqli_query($conn, "UPDATE tb_daftar_ulang SET
                no_daftar=$no_daftar,
                nama_pemohon='$nama_pemohon',
                keperluan='$keperluan',
                hari_datang='$hari_datang',
                tgl_datang='$tgl_datang',
                ktp='$ktp', kk='$kk', ijazah_akte='$ijazah',
                keterangan='$keterangan',
                no_antrian=$noAntrianSql
                WHERE id=$edit_id");
        } else {
            if ($keterangan === 'OK') {
                $resA = mysqli_query($conn, "SELECT MAX(no_antrian) AS mx FROM tb_daftar_ulang");
                $rowA = mysqli_fetch_assoc($resA);
                $no_antrian = ((int)$rowA['mx']) + 1;
                $noAntrianSql = $no_antrian;
            } else {
                $noAntrianSql = "NULL";
            }

            mysqli_query($conn, "INSERT INTO tb_daftar_ulang
                (no_daftar, nama_pemohon, keperluan, hari_datang, tgl_datang, ktp, kk, ijazah_akte, keterangan, no_antrian)
                VALUES ($no_daftar, '$nama_pemohon', '$keperluan', '$hari_datang', '$tgl_datang', '$ktp', '$kk', '$ijazah', '$keterangan', $noAntrianSql)");
        }
    }
    header("Location: daftar_ulang.php");
    exit;
}

// ----------------------------------------------------------------
// Ambil data untuk mode edit
// ----------------------------------------------------------------
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM tb_daftar_ulang WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($res);
}

$data = mysqli_query($conn, "SELECT * FROM tb_daftar_ulang ORDER BY id DESC");

$hari_options = ["Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu"];
$keperluan_options = ["Paspor Baru","Perpanjangan","Penggantian Rusak","Penggantian Hilang","Penambahan Halaman"];

$active = 'daftar_ulang';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar Ulang - Pengajuan Paspor</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">

    <div class="info-box">
        <b>Validasi:</b> Hari &amp; Tanggal Datang yang diinput akan dicocokkan dengan jadwal kedatangan hasil
        pendaftaran. Jika sesuai &rarr; Keterangan = <b>OK</b> dan pemohon mendapat No. Antrian otomatis.
        Jika tidak sesuai &rarr; Keterangan = <b>Tidak</b> (tanpa No. Antrian).
    </div>

    <div class="card">
        <h2><?php echo $edit_data ? 'Ubah Data Daftar Ulang' : 'Input Daftar Ulang'; ?></h2>
        <form method="POST" action="daftar_ulang.php">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id_edit" value="<?php echo $edit_data['id']; ?>">
            <?php endif; ?>

            <div class="form-row">
                <label>No. Daftar</label>
                <select name="no_daftar" required>
                    <option value="">-- pilih no. daftar --</option>
                    <?php
                    mysqli_data_seek($daftar_list, 0);
                    while ($d = mysqli_fetch_assoc($daftar_list)):
                        $sel = ($edit_data && $edit_data['no_daftar'] == $d['no_daftar']) ? 'selected' : '';
                    ?>
                        <option value="<?php echo $d['no_daftar']; ?>" <?php echo $sel; ?>
                            data-hari="<?php echo $d['hari']; ?>" data-tanggal="<?php echo $d['tanggal_datang']; ?>">
                            <?php echo $d['no_daftar'] . ' - ' . htmlspecialchars($d['nama_pemohon']) . ' (jadwal: ' . $d['hari'] . ', ' . $d['tanggal_datang'] . ')'; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-row">
                <label>Keperluan</label>
                <select name="keperluan" required>
                    <option value="">-- pilih keperluan --</option>
                    <?php foreach ($keperluan_options as $k): $sel = ($edit_data && $edit_data['keperluan'] === $k) ? 'selected' : ''; ?>
                        <option value="<?php echo $k; ?>" <?php echo $sel; ?>><?php echo $k; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <label>Hari Datang</label>
                <select name="hari_datang" required>
                    <option value="">-- pilih hari --</option>
                    <?php foreach ($hari_options as $h): $sel = ($edit_data && $edit_data['hari_datang'] === $h) ? 'selected' : ''; ?>
                        <option value="<?php echo $h; ?>" <?php echo $sel; ?>><?php echo $h; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <label>Tgl Datang</label>
                <input type="date" name="tgl_datang" required
                       value="<?php echo $edit_data ? $edit_data['tgl_datang'] : date('Y-m-d'); ?>">
            </div>

            <div class="form-row">
                <label>Berkas</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="ktp" <?php echo ($edit_data && $edit_data['ktp']=='Ada') ? 'checked' : ''; ?>> KTP</label>
                    <label><input type="checkbox" name="kk" <?php echo ($edit_data && $edit_data['kk']=='Ada') ? 'checked' : ''; ?>> KK</label>
                    <label><input type="checkbox" name="ijazah" <?php echo ($edit_data && $edit_data['ijazah_akte']=='Ada') ? 'checked' : ''; ?>> Ijazah/Akte</label>
                </div>
            </div>

            <button type="submit" class="btn"><?php echo $edit_data ? 'Update' : 'Simpan'; ?></button>
            <?php if ($edit_data): ?>
                <a href="daftar_ulang.php" class="btn secondary" style="text-decoration:none;display:inline-block;">Batal</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h2>Data Pendaftar Ulang</h2>
        <table>
            <tr>
                <th>No. Daftar</th>
                <th>Nama Pemohon</th>
                <th>Keperluan</th>
                <th>KTP</th>
                <th>KK</th>
                <th>Ijazah/Akte</th>
                <th>Keterangan</th>
                <th>No. Antrian</th>
                <th>Action</th>
            </tr>
            <?php if (mysqli_num_rows($data) == 0): ?>
                <tr><td colspan="9" class="empty">Belum ada data daftar ulang</td></tr>
            <?php else: while ($r = mysqli_fetch_assoc($data)): ?>
                <tr>
                    <td><?php echo $r['no_daftar']; ?></td>
                    <td><?php echo htmlspecialchars($r['nama_pemohon']); ?></td>
                    <td><?php echo htmlspecialchars($r['keperluan']); ?></td>
                    <td><?php echo $r['ktp']; ?></td>
                    <td><?php echo $r['kk']; ?></td>
                    <td><?php echo $r['ijazah_akte']; ?></td>
                    <td><span class="badge <?php echo $r['keterangan']=='OK'?'ok':'tidak'; ?>"><?php echo $r['keterangan']; ?></span></td>
                    <td><?php echo $r['no_antrian'] ?? '-'; ?></td>
                    <td class="action">
                        <a class="edit" href="daftar_ulang.php?edit=<?php echo $r['id']; ?>">edit</a>
                        <a class="hapus" href="daftar_ulang.php?hapus=<?php echo $r['id']; ?>"
                           onclick="return confirm('Hapus data ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; endif; ?>
        </table>
    </div>

</div>
</body>
</html>
