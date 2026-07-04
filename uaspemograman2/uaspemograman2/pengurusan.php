<?php
require 'config.php';

// ----------------------------------------------------------------
// Proses satu antrian dari Daftar Ulang menjadi data Pengurusan
// Logika:
// - Jika KTP, KK, Ijazah/Akte semuanya "Ada" -> Berkas=Lengkap,
//   Status=Diterima, Keterangan=OK, Pembayaran=355000
// - Jika salah satu tidak ada -> Berkas=Tidak Lengkap,
//   Status=Ditolak, Keterangan=Kurang Lengkap, Pembayaran=0
// ----------------------------------------------------------------
if (isset($_GET['proses'])) {
    $id = (int)$_GET['proses'];
    $res = mysqli_query($conn, "SELECT * FROM tb_daftar_ulang WHERE id=$id AND keterangan='OK' AND no_antrian IS NOT NULL");
    $du = mysqli_fetch_assoc($res);

    if ($du) {
        $sudah = mysqli_query($conn, "SELECT id FROM tb_pengurusan WHERE daftar_ulang_id=$id");
        if (mysqli_num_rows($sudah) == 0) {
            if ($du['ktp'] === 'Ada' && $du['kk'] === 'Ada' && $du['ijazah_akte'] === 'Ada') {
                $berkas = 'Lengkap';
                $status = 'Diterima';
                $ket = 'OK';
                $bayar = 355000;
            } else {
                $berkas = 'Tidak Lengkap';
                $status = 'Ditolak';
                $ket = 'Kurang Lengkap';
                $bayar = 0;
            }
            $nama = mysqli_real_escape_string($conn, $du['nama_pemohon']);
            mysqli_query($conn, "INSERT INTO tb_pengurusan
                (daftar_ulang_id, no_antrian, no_daftar, nama_pemohon, berkas, status, keterangan, pembayaran)
                VALUES ($id, {$du['no_antrian']}, {$du['no_daftar']}, '$nama', '$berkas', '$status', '$ket', $bayar)");
        }
    }
    header("Location: pengurusan.php");
    exit;
}

// ----------------------------------------------------------------
// Hapus data pengurusan
// ----------------------------------------------------------------
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM tb_pengurusan WHERE id=$id");
    header("Location: pengurusan.php");
    exit;
}

// Daftar antrian yang siap diproses (sudah OK, belum masuk tb_pengurusan)
$siap_proses = mysqli_query($conn, "
    SELECT du.* FROM tb_daftar_ulang du
    LEFT JOIN tb_pengurusan p ON p.daftar_ulang_id = du.id
    WHERE du.keterangan='OK' AND du.no_antrian IS NOT NULL AND p.id IS NULL
    ORDER BY du.no_antrian ASC");

// Data pengurusan yang sudah diproses
$data = mysqli_query($conn, "SELECT * FROM tb_pengurusan ORDER BY no_antrian ASC");

// Total pendapatan (hanya dari yang status Diterima)
$pendapatanRes = mysqli_query($conn, "SELECT SUM(pembayaran) AS total FROM tb_pengurusan WHERE status='Diterima'");
$pendapatanRow = mysqli_fetch_assoc($pendapatanRes);
$pendapatan = $pendapatanRow['total'] ?? 0;

$active = 'pengurusan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pengurusan - Pengajuan Paspor</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">

    <div class="info-box">
        <b>Logika Pengurusan:</b> Jika KTP, KK, dan Ijazah/Akte semuanya lengkap &rarr; Berkas = Lengkap,
        Status = Diterima, Keterangan = OK, Pembayaran = Rp355.000. Jika ada berkas yang kurang &rarr;
        Berkas = Tidak Lengkap, Status = Ditolak, Keterangan = Kurang Lengkap, Pembayaran = Rp0.
    </div>

    <div class="card">
        <h2>Antrian Siap Diproses</h2>
        <table>
            <tr>
                <th>No. Antrian</th>
                <th>No. Daftar</th>
                <th>Nama Pemohon</th>
                <th>KTP</th>
                <th>KK</th>
                <th>Ijazah/Akte</th>
                <th>Action</th>
            </tr>
            <?php if (mysqli_num_rows($siap_proses) == 0): ?>
                <tr><td colspan="7" class="empty">Tidak ada antrian yang siap diproses</td></tr>
            <?php else: while ($r = mysqli_fetch_assoc($siap_proses)): ?>
                <tr>
                    <td><?php echo $r['no_antrian']; ?></td>
                    <td><?php echo $r['no_daftar']; ?></td>
                    <td><?php echo htmlspecialchars($r['nama_pemohon']); ?></td>
                    <td><?php echo $r['ktp']; ?></td>
                    <td><?php echo $r['kk']; ?></td>
                    <td><?php echo $r['ijazah_akte']; ?></td>
                    <td class="action">
                        <a class="proses" href="pengurusan.php?proses=<?php echo $r['id']; ?>">Proses</a>
                    </td>
                </tr>
            <?php endwhile; endif; ?>
        </table>
    </div>

    <div class="card">
        <h2>Data Pengurusan Paspor</h2>
        <table>
            <tr>
                <th>No. Antrian</th>
                <th>No. Daftar</th>
                <th>Nama Pemohon</th>
                <th>Berkas</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th>Pembayaran</th>
                <th>Action</th>
            </tr>
            <?php if (mysqli_num_rows($data) == 0): ?>
                <tr><td colspan="8" class="empty">Belum ada data pengurusan</td></tr>
            <?php else: while ($r = mysqli_fetch_assoc($data)): ?>
                <tr>
                    <td><?php echo $r['no_antrian']; ?></td>
                    <td><?php echo $r['no_daftar']; ?></td>
                    <td><?php echo htmlspecialchars($r['nama_pemohon']); ?></td>
                    <td><?php echo $r['berkas']; ?></td>
                    <td><span class="badge <?php echo $r['status']=='Diterima'?'ok':'tidak'; ?>"><?php echo $r['status']; ?></span></td>
                    <td><?php echo $r['keterangan']; ?></td>
                    <td>Rp<?php echo number_format($r['pembayaran'],0,',','.'); ?></td>
                    <td class="action">
                        <a class="hapus" href="pengurusan.php?hapus=<?php echo $r['id']; ?>"
                           onclick="return confirm('Hapus data ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; endif; ?>
        </table>
        <div class="pendapatan">Pendapatan: <span>Rp<?php echo number_format($pendapatan,0,',','.'); ?></span></div>
    </div>

</div>
</body>
</html>
