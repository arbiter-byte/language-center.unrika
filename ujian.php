<?php
// Pastikan sesi dimulai
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

// Sertakan file koneksi database (diperlukan untuk konteks aplikasi)
include 'config.php';

// Data simulasi untuk pertanyaan Listening
// Dalam aplikasi nyata, data ini akan diambil dari database
$questions = [
    1 => [
        'text' => '... is the capital of Indonesia.',
        'options' => ['That', 'This', 'Were', 'What'],
        'correct_answer' => 'This' // Contoh: Jawaban A
    ],
    2 => [
        'text' => 'The book ... on the table.',
        'options' => ['are', 'is', 'it', 'what'],
        'correct_answer' => 'is' // Contoh: Jawaban B
    ],
    3 => [
        'text' => '... are going to the cinema tonight.',
        'options' => ['I', 'You', 'They', 'We'],
        'correct_answer' => 'We' // Contoh: Jawaban D
    ],
    4 => [
        'text' => '... is my friend, Sarah.',
        'options' => ['A (He)', 'B (She)', 'C (It)', 'D (Is)'],
        'correct_answer' => 'She' // Contoh: Jawaban B
    ],
    // Tambahkan lebih banyak pertanyaan sesuai kebutuhan
];

// Inisialisasi data jawaban pengguna (jika diperlukan untuk menyimpan state)
$user_answers = $_SESSION['user_answers'] ?? [];

// Waktu total ujian (120 menit = 7200 detik), sesuai coretan di wireframe 02:00:00
$total_time_seconds = 7200; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listening Section - Ujian TOEFL</title>
    <link rel="stylesheet" href="u_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Tambahan styling untuk tata letak ujian */
        .exam-page-container {
            display: flex;
            width: 100%;
            height: 100%;
        }

        .exam-main-area {
            flex-grow: 1;
            padding: 20px 40px;
            /* Memastikan area pertanyaan tidak terlalu lebar */
            max-width: 850px; 
        }

        .exam-sidebar {
            width: 250px; /* Lebar untuk Timer */
            padding: 30px 20px;
            border-left: 1px solid var(--shadow-dark);
            text-align: center;
            /* Gaya yang diangkat (raised) untuk sidebar timer */
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.05); 
        }

        /* --- Header Listening Section --- */
        .section-header {
            font-size: 2.5em;
            font-weight: 800;
            color: var(--text-color);
            margin-bottom: 30px;
            padding-bottom: 10px;
            text-align: center;
            border-bottom: 4px solid var(--accent-color);
            letter-spacing: 2px;
        }

        /* --- Audio Player Simulation (Sesuai Wireframe) --- */
        .audio-player-box {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px 0;
            border-radius: 15px;
            background: var(--main-color);
            box-shadow: inset 4px 4px 8px var(--shadow-dark), 
                        inset -4px -4px 8px var(--shadow-light);
        }

        .play-button-icon {
            font-size: 4em;
            color: var(--accent-color);
            cursor: pointer;
        }

        .play-text {
            display: block;
            margin-top: 10px;
            font-weight: 600;
            color: var(--text-color);
        }

        /* --- Questions Layout (Dua Kolom) --- */
        .questions-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px; /* Jarak antar kolom */
            padding-bottom: 80px; /* Ruang untuk tombol NEXT */
        }

        .question-block {
            width: calc(50% - 10px); /* 50% dikurangi setengah gap */
            margin-bottom: 20px;
            /* Padding untuk membedakan antar soal */
            padding-bottom: 15px; 
            border-bottom: 1px dashed var(--shadow-dark);
        }

        .question-number {
            font-size: 1.3em;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--accent-color);
        }

        .options-list {
            display: flex;
            flex-direction: column;
            gap: 8px; /* Jarak antar pilihan */
        }

        /* Menggunakan ulang styling opsi dari u_style.css */
        .option-label {
            padding: 10px 15px;
            font-size: 1em;
        }

        /* --- Timer Styling --- */
        .timer-box {
            margin-top: 50px;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 4px 4px 8px var(--shadow-dark),
                        -4px -4px 8px var(--shadow-light);
        }
        
        .timer-box h3 {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text-color);
        }

        .timer-display {
            font-size: 2.2em;
            font-weight: 900;
            color: var(--error-color); /* Warna merah untuk Timer */
        }

        /* Tombol NEXT/Navigation */
        .navigation-buttons {
            width: 100%;
            position: fixed; /* Fixed agar selalu terlihat */
            bottom: 0;
            left: 0;
            padding: 15px 40px;
            background: var(--main-color);
            border-top: 1px solid var(--shadow-dark);
            display: flex;
            justify-content: flex-end;
            box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.1);
            z-index: 10; 
        }

    </style>
</head>
<body>

    <div class="content-card neumo-raised" style="width: 100%; max-width: none; min-height: 100vh; padding: 0;">
        <div class="exam-page-container">
            
            <div class="exam-main-area">
                <h1 class="section-header">LISTENING</h1>

                <div class="audio-player-box">
                    <i class="fas fa-play-circle play-button-icon"></i>
                    <span class="play-text">PLAY AUDIO</span>
                </div>
                
                <form action="save_answers.php" method="POST">
                    <div class="questions-grid">
                        
                        <?php foreach ($questions as $number => $q): ?>
                            <div class="question-block">
                                <p class="question-number"><?php echo $number; ?>. <?php echo htmlspecialchars($q['text']); ?></p>
                                
                                <div class="options-list">
                                    <?php 
                                    $option_letters = ['A', 'B', 'C', 'D', 'E'];
                                    foreach ($q['options'] as $index => $option): 
                                        $option_id = "q{$number}_opt{$index}";
                                        $value = $option_letters[$index]; // Nilai yang dikirim saat submit
                                    ?>
                                        <div class="option-item">
                                            <input type="radio" id="<?php echo $option_id; ?>" name="answer_<?php echo $number; ?>" value="<?php echo $value; ?>" 
                                                <?php echo (isset($user_answers[$number]) && $user_answers[$number] == $value) ? 'checked' : ''; ?>>
                                            <label for="<?php echo $option_id; ?>" class="neumo-raised option-label">
                                                (<?php echo $value; ?>) <?php echo htmlspecialchars($option); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                    </div>

                    <div class="navigation-buttons">
                        <button type="submit" class="neumo-button neumo-raised next-button">
                            NEXT (Structure) <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                </form>
            </div>
            
            <div class="exam-sidebar">
                <div class="timer-box neumo-raised">
                    <h3>TIMER (Hitungan mundur)</h3>
                    <div class="timer-display" id="exam-timer">02:00:00</div>
                </div>
                </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Waktu awal (dalam detik)
            let timeInSeconds = <?php echo $total_time_seconds; ?>; 
            const timerDisplay = document.getElementById('exam-timer');

            function updateTimer() {
                const hours = Math.floor(timeInSeconds / 3600);
                const minutes = Math.floor((timeInSeconds % 3600) / 60);
                const seconds = timeInSeconds % 60;

                const formattedTime = 
                    String(hours).padStart(2, '0') + ':' + 
                    String(minutes).padStart(2, '0') + ':' + 
                    String(seconds).padStart(2, '0');

                timerDisplay.textContent = formattedTime;

                if (timeInSeconds <= 0) {
                    clearInterval(timerInterval);
                    alert("Waktu ujian habis! Jawaban akan disubmit otomatis.");
                    // Lakukan submit form di sini
                    // document.querySelector('form').submit();
                } else {
                    timeInSeconds--;
                }
            }

            // Jalankan timer setiap detik
            const timerInterval = setInterval(updateTimer, 1000);
            updateTimer(); // Panggil sekali saat start
        });
    </script>
</body>
</html>