<?php

// Konfigurasi Database
$db_host = 'localhost';
$db_user = 'root'; // Ganti dengan username database Anda
$db_pass = ''; // Ganti dengan password database Anda (biasanya kosong untuk XAMPP)
$db_name = 'toefl';

// Buat koneksi ke database menggunakan MySQLi
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

?>