<?php
// Pastikan sesi dimulai untuk manajemen login
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_name'])) {
    // Arahkan ke halaman login jika belum login
    header("Location: login.php"); // Ganti 'login.php' sesuai nama file login Anda
    exit();
}

// Sertakan file koneksi database
include 'config.php';

// Ambil data pengguna dari database
$user_name = $_SESSION['user_name'];
$sql_user = "SELECT nama_peserta, npm, email FROM data_user WHERE user_name = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("s", $user_name);
$stmt->execute();
$result_user = $stmt->get_result();
$user_data = $result_user->fetch_assoc();
$stmt->close();



// Default nama pengguna jika data tidak ditemukan (seharusnya tidak terjadi)
$nama_peserta = $user_data ? htmlspecialchars($user_data['nama_peserta']) : "Pengguna";

// Tentukan halaman yang akan ditampilkan (default: dashboard)
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Fungsi untuk me-logout pengguna
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset();
    session_destroy();
    header("Location: login.php"); // Arahkan kembali ke halaman login
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna - Sistem Ujian TOEFL</title>
    <link rel="stylesheet" href="u_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <div class="sidebar">
        <div class="logo">
            <img src="logo-lc.png" alt="Language Center UNRIKA Logo">
            
            <p style="text-align: center; font-weight: 600; font-size: 0.9em; margin-bottom: 40px;">
                Language Center<br>Universitas Riau Kepulauan
            </p>
        </div>

        <div class="menu">
            <a href="?page=dashboard" class="neumo-raised neumo-button 
                <?php echo ($page == 'dashboard' ? 'active-menu' : ''); ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
            
            <a href="?page=profile" class="neumo-raised neumo-button 
                <?php echo ($page == 'profile' ? 'active-menu' : ''); ?>">
                <i class="fas fa-user"></i> Profile
            </a>
            
            <a href="?page=hasil" class="neumo-raised neumo-button 
                <?php echo ($page == 'hasil' ? 'active-menu' : ''); ?>">
                <i class="fas fa-poll"></i> Hasil Ujian
            </a>
            
            <a href="?action=logout" class="neumo-raised neumo-button logout-button">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    <div class="main-content">
        <div class="content-card neumo-raised">

            <?php if ($page == 'dashboard'): ?>
                <div class="dashboard-content" style="text-align: center;">
                    <h1 class="header" style="text-align: left;">
                        Hello! (<?php echo $nama_peserta; ?>)
                    </h1>
                    
                    <h2 style="margin-top: 50px; font-size: 1.8em; line-height: 1.5;">
                        Selamat Datang! di sistem Ujian TOEFL. <br>Apakah anda siap untuk diujian?
                    </h2>
                    
                    <a href="?page=instruksi" class="neumo-button neumo-raised" style="
    margin: 60px auto; 
    padding: 25px 80px; 
    font-size: 1.5em; 
    border-radius: 50px; 
    background-color: var(--accent-color); 
    color: var(--shadow-light); 
    box-shadow: 8px 8px 16px var(--shadow-dark), -8px -8px 16px var(--shadow-light);
">
    START
</a>
                    
                    <h3 style="margin-top: 50px; font-size: 1.5em; color: var(--accent-color);">
                        Semoga Mendapatkan Hasil <br>Yang Memuaskan !!!
                    </h3>
                </div>

                <?php elseif ($page == 'instruksi'): ?>
                <div class="instruction-content" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    width: 100%;
                ">
                    <h1 class="header" style="
                        font-size: 2em;
                        font-weight: 700;
                        color: var(--text-color);
                        margin-bottom: 30px;
                        padding-bottom: 10px;
                        border-bottom: none; /* Hapus garis header */
                        text-align: left;
                        width: 100%;
                    ">
                        Lorem Ipsum - Instruksi Ujian
                    </h1>
                    
                    <div class="instruction-list" style="
                        width: 100%;
                        max-width: 700px; /* Batasi lebar konten agar seperti gambar */
                        margin-bottom: 50px;
                    ">
                        <?php
                        // Data simulasi instruksi yang mereplikasi 'garis' konten
                        $instruction_lines = [
                            "Ujian ini dibagi menjadi tiga seksi utama: Listening, Structure, dan Reading.",
                            "Waktu total yang diberikan adalah 120 menit dan akan dihitung mundur secara ketat.",
                            "Tidak ada jeda antar seksi. Pastikan Anda siap sebelum memulai.",
                            "Pastikan koneksi internet Anda stabil. Gangguan dapat mengakhiri sesi ujian.",
                            "Dilarang menggunakan sumber daya eksternal, termasuk kamus atau buku.",
                            "Anda harus menyelesaikan setiap seksi secara berurutan dan tidak bisa kembali.",
                            "Pilih jawaban terbaik untuk setiap pertanyaan dengan mengklik opsi yang tersedia.",
                            "Klik tombol NEXT di bawah untuk memulai seksi Listening."
                        ];
                        
                        foreach ($instruction_lines as $line) {
                            echo '<p class="neumo-raised instruction-line" style="';
                            echo 'padding: 15px 20px; margin: 15px 0; text-align: left; font-size: 1.05em; border-radius: 8px;';
                            echo 'box-shadow: inset 1px 1px 3px var(--shadow-dark), inset -1px -1px 3px var(--shadow-light);';
                            echo '">';
                            echo htmlspecialchars($line);
                            echo '</p>';
                        }
                        ?>
                    </div>
                    
                    <a href="ujian.php" class="neumo-button neumo-raised" style="
                        padding: 25px 80px; 
                        font-size: 1.5em; 
                        border-radius: 50px; 
                        background-color: var(--accent-color); 
                        color: var(--shadow-light); 
                        box-shadow: 8px 8px 16px var(--shadow-dark), -8px -8px 16px var(--shadow-light);
                    ">
                        NEXT
                    </a>
                </div>

            <?php elseif ($page == 'profile' && $user_data): ?>

            <?php elseif ($page == 'profile' && $user_data): ?>
                <h1 class="header">Profile Pengguna</h1>

                <div class="profile-detail-card neumo-raised">
                    <table class="profile-table">
                        <tr>
                            <th>Nama Peserta</th>
                            <td><?php echo $nama_peserta; ?></td>
                        </tr>
                        <tr>
                            <th>NPM</th>
                            <td><?php echo htmlspecialchars($user_data['npm']); ?></td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td><?php echo htmlspecialchars($_SESSION['user_name']); ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?php echo htmlspecialchars($user_data['email']); ?></td>
                        </tr>
                    </table>
                </div>

            <?php elseif ($page == 'hasil'): ?>
                <h1 class="header">Hasil Ujian Anda</h1>
                
                <div class="result-box neumo-raised">
                    <h2>Skor Ujian Terakhir Anda</h2>
                    <div class="score neumo-raised">550</div> 
                    <p class="summary-text">Selamat! Anda telah menyelesaikan ujian TOEFL.</p>
                    <p class="summary-text">Detail nilai per bagian dapat dilihat di bawah ini.</p>
                    
                    <table class="profile-table result-recap-table" style="width: 60%; margin: 30px auto;">
                        <tr>
                            <th>Listening</th>
                            <td>50</td>
                        </tr>
                        <tr>
                            <th>Structure</th>
                            <td>55</td>
                        </tr>
                        <tr>
                            <th>Reading</th>
                            <td>60</td>
                        </tr>
                    </table>
                </div>

            <?php else: ?>
                <h1 class="header">Halaman Tidak Ditemukan</h1>
                <p>Silakan pilih menu di samping.</p>
            <?php endif; ?>

        </div>
    </div>
    </body>
</html>