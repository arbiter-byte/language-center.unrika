<?php
// Sertakan file konfigurasi database
include 'config.php';
// Mulai sesi untuk menyimpan status login (jika berhasil)
session_start();

// Cek apakah form sudah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form dan sanitasi (kebersihan data)
    // Menggunakan mysqli_real_escape_string untuk mencegah SQL Injection
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password']; // Password akan di-hash atau diverifikasi, jadi tidak perlu real_escape_string untuk password mentah

    // Catatan: Dalam praktek nyata, password harus disimpan dalam database dalam bentuk ter-hash (misalnya menggunakan password_hash()).
    // Karena struktur tabel Anda menggunakan kolom 'user_pass' VARCHAR(25) yang menyiratkan password disimpan sebagai teks biasa (atau hash sederhana),
    // kita akan mencocokkan secara langsung. Namun, sangat disarankan untuk menggunakan password_hash() dan password_verify() untuk keamanan.

    // Query untuk mencari user dengan username yang cocok
    // Kolom user_name pada tabel data_user
    $sql = "SELECT user_pass FROM data_user WHERE user_name = ?";
    
    // Gunakan Prepared Statements untuk keamanan yang lebih baik (mencegah SQL Injection)
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User ditemukan, ambil data password dari database
        $row = $result->fetch_assoc();
        $hashed_password = $row['user_pass']; // Ambil password (yang diasumsikan plaintext atau hash sederhana)

        // Verifikasi Password
        // Dalam skenario ini, karena kolom 'user_pass' adalah VARCHAR(25),
        // kita berasumsi password disimpan sebagai teks biasa, jadi kita bandingkan langsung.
        // Jika Anda menggunakan password_hash(), ganti dengan: if (password_verify($password, $hashed_password)) { ... }
        if ($password === $hashed_password) {
            // Login Berhasil
            
            // Ambil detail user lain jika diperlukan (untuk sesi)
            // Query untuk mendapatkan semua detail user yang login
            $sql_detail = "SELECT * FROM data_user WHERE user_name = ?";
            $stmt_detail = $conn->prepare($sql_detail);
            $stmt_detail->bind_param("s", $username);
            $stmt_detail->execute();
            $result_detail = $stmt_detail->get_result();
            $user_data = $result_detail->fetch_assoc();
            
            // Simpan informasi user ke dalam SESSION
            $_SESSION['loggedin'] = true;
            $_SESSION['user_name'] = $user_data['user_name'];
            $_SESSION['nama_peserta'] = $user_data['nama_peserta'];
            // Anda bisa menyimpan data lain yang dibutuhkan di dashboard
            
            // Arahkan ke halaman dashboard_user.php
            header("Location: dashboard_user.php");
            exit;
        } else {
            // Password Salah
            $error_message = "Maaf! username atau password yang anda masukkan salah silahkan hubungi admin!";
        }
    } else {
        // Username Tidak Ditemukan
        $error_message = "Maaf! username atau password yang anda masukkan salah silahkan hubungi admin!";
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pengguna</title>
    <link rel="stylesheet" href="style.css">
    <?php if (isset($error_message)): ?>
    <script>
        // Tampilkan pop up (alert) jika ada pesan error
        alert("<?php echo $error_message; ?>");
    </script>
    <?php endif; ?>
</head>
<body>
    <div class="login-container">
        <h1 class="login-title">Login</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="login-form">
            <div class="input-group">
                <i class="icon fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <i class="icon fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="login-button">Login</button>
        </form>
    </div>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>