CREATE DATABASE latihan12;

USE latihan12;

CREATE TABLE mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    umur INT
);

INSERT INTO mahasiswa(nama,umur)
VALUES
('Karina',20),
('Budi',22),
('Prabowo',25);