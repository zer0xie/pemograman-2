-- ==========================================================
-- DATABASE: Pengajuan Paspor - Kantor Imigrasi Cabang
-- UAS Pemrograman Web II
-- ==========================================================

CREATE DATABASE IF NOT EXISTS db_paspor;
USE db_paspor;

-- 1. Tabel Pendaftaran (Modul Daftar)
CREATE TABLE IF NOT EXISTS tb_daftar (
    no_daftar INT AUTO_INCREMENT PRIMARY KEY,
    nama_pemohon VARCHAR(100) NOT NULL,
    tgl_daftar DATE NOT NULL,          -- tanggal saat pemohon mendaftar
    hari VARCHAR(20) NOT NULL,         -- hari harus datang (hasil penjadwalan otomatis)
    tanggal_datang DATE NOT NULL,      -- tanggal harus datang (hasil penjadwalan otomatis)
    jam TIME NOT NULL                  -- jam harus datang (hasil penjadwalan otomatis)
);

-- 2. Tabel Daftar Ulang (Modul Daftar Ulang)
CREATE TABLE IF NOT EXISTS tb_daftar_ulang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_daftar INT NOT NULL,
    nama_pemohon VARCHAR(100) NOT NULL,
    keperluan VARCHAR(50) NOT NULL,
    hari_datang VARCHAR(20) NOT NULL,  -- hari kedatangan aktual (input petugas)
    tgl_datang DATE NOT NULL,          -- tanggal kedatangan aktual (input petugas)
    ktp ENUM('Ada','Tidak') NOT NULL DEFAULT 'Tidak',
    kk ENUM('Ada','Tidak') NOT NULL DEFAULT 'Tidak',
    ijazah_akte ENUM('Ada','Tidak') NOT NULL DEFAULT 'Tidak',
    keterangan VARCHAR(10) NOT NULL,   -- OK / Tidak (hasil validasi jadwal)
    no_antrian INT NULL,               -- nomor antrian otomatis, hanya terisi jika keterangan = OK
    FOREIGN KEY (no_daftar) REFERENCES tb_daftar(no_daftar) ON DELETE CASCADE
);

-- 3. Tabel Pengurusan Paspor (Modul Pengurusan)
CREATE TABLE IF NOT EXISTS tb_pengurusan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    daftar_ulang_id INT NOT NULL,
    no_antrian INT NOT NULL,
    no_daftar INT NOT NULL,
    nama_pemohon VARCHAR(100) NOT NULL,
    berkas VARCHAR(20) NOT NULL,       -- Lengkap / Tidak Lengkap
    status VARCHAR(20) NOT NULL,       -- Diterima / Ditolak
    keterangan VARCHAR(20) NOT NULL,   -- OK / Kurang Lengkap
    pembayaran INT NOT NULL DEFAULT 0, -- 355000 jika lengkap, 0 jika tidak
    FOREIGN KEY (daftar_ulang_id) REFERENCES tb_daftar_ulang(id) ON DELETE CASCADE
);
