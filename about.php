<?php
session_start();
include 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tentang - TomatoLite</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>TomatoLite About</h1>
    </header>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin/dashboard.php">Admin Dashboard</a></li>
                <?php else: ?>
                    <li><a href="user/dashboard.php">Dashboard</a></li>
                <?php endif; ?>
                <li><a href="about.php">About</a></li>
                <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="about.php">About</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="container">
        <div class="about-section">
            <h2>Tentang TomatoLite</h2>
            <p>
                Selamat datang di <strong>TomatoLite</strong>, platform ulasan film sederhana yang dikembangkan untuk pecinta sinema! Kami percaya bahwa setiap pendapat tentang film itu berharga. Oleh karena itu, TomatoLite hadir sebagai wadah bagi Anda untuk mengekspresikan pandangan, berbagi rating, dan membaca ulasan dari komunitas.
            </p>
            <p>
                Terinspirasi dari semangat memberikan gambaran sejelas mungkin tentang sebuah film, kami merancang TomatoLite agar mudah digunakan, intuitif, dan fokus pada inti dari pengalaman menonton film: bagaimana rasanya dan apa yang orang lain pikirkan. Baik Anda mencari rekomendasi atau ingin memberikan kritik membangun, TomatoLite adalah tempatnya.
            </p>
            <p>
                Proyek ini dibuat sebagai bagian dari tugas pengembangan web, dan kami sangat senang bisa menghadirkan fungsionalitas dasar dari sebuah situs review film menggunakan HTML, CSS, JavaScript, PHP, dan MySQL. Kami terus berupaya untuk meningkatkan pengalaman pengguna dan menambahkan fitur-fitur menarik di masa mendatang.
            </p>

            <h2>Fitur Utama</h2>
            <div class="features-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;">
                <div style="background: #f4f4f4; padding: 20px; border-radius: 8px;">
                    <h4>📝 Review & Rating</h4>
                    <p>Berikan ulasan dan rating untuk film favorit Anda. Bagikan pengalaman menonton dengan komunitas.</p>
                </div>
                <div style="background: #f4f4f4; padding: 20px; border-radius: 8px;">
                    <h4>🎬 Database Film</h4>
                    <p>Jelajahi koleksi film dengan informasi lengkap termasuk poster, deskripsi, dan tahun rilis.</p>
                </div>
                <div style="background: #f4f4f4; padding: 20px; border-radius: 8px;">
                    <h4>👥 Komunitas</h4>
                    <p>Baca ulasan dari pengguna lain dan temukan film baru berdasarkan rekomendasi komunitas.</p>
                </div>
                <div style="background: #f4f4f4; padding: 20px; border-radius: 8px;">
                    <h4>📊 Dashboard Personal</h4>
                    <p>Kelola ulasan Anda sendiri dan lacak aktivitas review dalam dashboard pribadi.</p>
                </div>
            </div>

            <h2>Tim Pengembang</h2>
            <p>
                TomatoLite dikembangkan dengan semangat kolaborasi dan dedikasi oleh tim kecil yang bersemangat tentang teknologi dan film.
            </p>
            <div class="developer-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
                <div class="developer-card" style="background: #f9f9f9; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <img src="https://via.placeholder.com/100/FF5733/FFFFFF?text=A" alt="Foto Developer A" style="border-radius: 50%; margin-bottom: 15px;">
                    <h4>Aulia Rahman</h4>
                    <p><strong>Peran:</strong> Project Lead, Backend Developer</p>
                    <p>Aulia adalah otak di balik logika server dan manajemen database TomatoLite. Ia memastikan semua data film dan ulasan tersimpan dengan aman dan efisien.</p>
                </div>
                <div class="developer-card" style="background: #f9f9f9; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <img src="https://via.placeholder.com/100/33AFFF/FFFFFF?text=B" alt="Foto Developer B" style="border-radius: 50%; margin-bottom: 15px;">
                    <h4>Budi Santoso</h4>
                    <p><strong>Peran:</strong> Frontend Developer, UI/UX Designer</p>
                    <p>Budi bertanggung jawab atas tampilan visual dan pengalaman pengguna TomatoLite. Ia merancang antarmuka yang bersih dan intuitif.</p>
                </div>
                <div class="developer-card" style="background: #f9f9f9; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <img src="https://via.placeholder.com/100/33FF57/FFFFFF?text=C" alt="Foto Developer C" style="border-radius: 50%; margin-bottom: 15px;">
                    <h4>Citra Dewi</h4>
                    <p><strong>Peran:</strong> Database Administrator, Tester</p>
                    <p>Citra mengelola skema database dan memastikan integritas data. Ia juga berperan penting dalam menguji fungsionalitas dan mencari bug.</p>
                </div>
                <div class="developer-card" style="background: #f9f9f9; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <img src="https://via.placeholder.com/100/FF33FF/FFFFFF?text=D" alt="Foto Developer D" style="border-radius: 50%; margin-bottom: 15px;">
                    <h4>Dedi Kurniawan</h4>
                    <p><strong>Peran:</strong> Content Manager, Dokumentasi</p>
                    <p>Dedi memastikan konten film awal termuat dengan baik dan membantu dalam penulisan dokumentasi teknis dan pengguna.</p>
                </div>
            </div>

            <h2>Teknologi yang Digunakan</h2>
            <div style="background: #e8f4f8; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <ul style="list-style: none; padding: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                    <li style="padding: 10px; background: white; border-radius: 5px; text-align: center;"><strong>Frontend:</strong> HTML5, CSS3, JavaScript</li>
                    <li style="padding: 10px; background: white; border-radius: 5px; text-align: center;"><strong>Backend:</strong> PHP 7+</li>
                    <li style="padding: 10px; background: white; border-radius: 5px; text-align: center;"><strong>Database:</strong> MySQL</li>
                    <li style="padding: 10px; background: white; border-radius: 5px; text-align: center;"><strong>Server:</strong> Apache/Nginx</li>
                </ul>
            </div>

            <h2>Kontak & Dukungan</h2>
            <p>
                Jika Anda memiliki pertanyaan, saran, atau menemukan bug, jangan ragu untuk menghubungi tim pengembang kami. 
                Kami selalu terbuka untuk masukan yang membangun untuk terus meningkatkan TomatoLite.
            </p>
            <div style="background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin: 20px 0;">
                <p><strong>📧 Email:</strong> support@tomatolite.com</p>
                <p><strong>🐛 Bug Report:</strong> Laporkan bug melalui halaman kontak atau email langsung</p>
                <p><strong>💡 Feature Request:</strong> Kirimkan ide fitur baru yang ingin Anda lihat di TomatoLite</p>
            </div>
        </div>
    </div>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> TomatoLite. All rights reserved.</p>
    </footer>
</body>
</html>
<?php $conn->close(); ?>