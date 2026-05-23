<!DOCTYPE html>
<html>
<head>
    <title>Form Input Data Mahasiswa | PHP MySQL Tutorial</title>
    <style type="text/css" media="screen">
        body {
            font-family: Verdana, Arial, Helvetica, sans-serif;
            font-size: 11px;
        }
        table {
            font-family: Verdana, Arial, Helvetica, sans-serif;
            font-size: 11px;
        }
        input, select {
            font-family: Verdana, Arial, Helvetica, sans-serif;
            font-size: 11px;
            height: 20px;
        }
        .container {
            border: 0;
            padding: 10px;
            width: 760px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="Pert13_Latihan2_Action.php" method="POST" name="form-input-data">
            <table width="760" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr height="46">
                    <td width="10%">&nbsp;</td>
                    <td width="25%">&nbsp;</td>
                    <td width="65%">
                        <font color="orange" size="2"><b>Form Input Data Mahasiswa</b></font>
                    </td>
                </tr>
                <tr height="46">
                    <td>&nbsp;</td>
                    <td>ID Mahasiswa / NIM</td>
                    <td>
                        <input type="text" name="id_mahasiswa" size="35" maxlength="10" placeholder="Masukkan NIM" required />
                    </td>
                </tr>
                <tr height="46">
                    <td>&nbsp;</td>
                    <td>Nama</td>
                    <td>
                        <input type="text" name="nama" size="50" maxlength="50" placeholder="Masukkan Nama Lengkap" required />
                    </td>
                </tr>
                <tr height="46">
                    <td>&nbsp;</td>
                    <td>Jurusan</td>
                    <td>
                        <select name="jurusan" required>
                            <option value="">- Pilih Jurusan -</option>
                            <option value="Teknik Komputer">Teknik Komputer</option>
                            <option value="Teknik Informatika">Teknik Informatika</option>
                            <option value="Teknik Mesin">Teknik Mesin</option>
                            <option value="Teknik Elektro">Teknik Elektro</option>
                            <option value="Komputer Akuntansi">Komputer Akuntansi</option>
                        </select>
                    </td>
                </tr>
                <tr height="46">
                    <td>&nbsp;</td>
                    <td>Alamat</td>
                    <td>
                        <input type="text" name="alamat" size="50" maxlength="100" placeholder="Masukkan Alamat" required />
                    </td>
                </tr>
                <tr height="46">
                    <td>&nbsp;</td>
                    <td>No. Telp</td>
                    <td>
                        <input type="text" name="telepon" size="20" maxlength="15" placeholder="Masukkan No. Telepon" required />
                    </td>
                </tr>
                <tr height="46">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>
                        <input type="submit" name="Submit" value="Submit">
                        &nbsp;&nbsp;
                        <input type="reset" name="reset" value="Cancel">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>