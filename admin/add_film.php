<?php
session_start();
include '../includes/db.php';
// Assuming check_admin() is included from auth.php
// If not, you might need to include '../includes/auth.php'; and call check_admin();
// include '../includes/auth.php';
// check_admin(); // Make sure this is called if needed for authentication

$message = '';

// Temporarily enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $release_year = intval($_POST['release_year']);

    // Handle file upload
    if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['poster_file']['tmp_name'];
        $file_type = $_FILES['poster_file']['type']; // e.g., image/jpeg, image/png
        $file_content = file_get_contents($file_tmp_name); // Read the binary content

        // Basic validation for image types (optional but recommended)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file_type, $allowed_types)) {
            $message = '<p class="error">Tipe file tidak didukung. Hanya gambar JPEG, PNG, GIF yang diizinkan.</p>';
        } elseif (empty($title) || empty($description) || $release_year < 1800) {
             $message = '<p class="error">Semua field film harus diisi dengan benar.</p>';
        } else {
            $stmt = $conn->prepare("INSERT INTO films (title, description, release_year, poster_image, poster_mime_type) VALUES (?, ?, ?, ?, ?)");
            // 'ssibs' means: string, string, integer, BLOB (binary data), string
            // The 'b' type for bind_param is crucial for BLOB data
            $stmt->bind_param("ssibs", $title, $description, $release_year, $file_content, $file_type);

            if ($stmt->execute()) {
                $message = '<p class="success">Film "' . htmlspecialchars($title) . '" berhasil ditambahkan!</p>';
                // Clear form fields after successful submission
                $_POST = array(); // Reset POST array to clear form values
            } else {
                $message = '<p class="error">Gagal menambahkan film: ' . $conn->error . '</p>';
            }
            $stmt->close();
        }
    } else {
        // More specific error message for upload failure
        if (isset($_FILES['poster_file'])) {
            switch ($_FILES['poster_file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $message = '<p class="error">Ukuran file terlalu besar. Periksa pengaturan upload_max_filesize dan post_max_size di php.ini.</p>';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $message = '<p class="error">File hanya terunggah sebagian.</p>';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $message = '<p class="error">Tidak ada file yang dipilih untuk diunggah.</p>';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $message = '<p class="error">Folder sementara untuk upload tidak ditemukan.</p>';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $message = '<p class="error">Gagal menulis file ke disk.</p>';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $message = '<p class="error">Ekstensi PHP menghentikan unggahan file.</p>';
                    break;
                default:
                    $message = '<p class="error">Gagal mengunggah file poster karena alasan yang tidak diketahui.</p>';
                    break;
            }
        } else {
            $message = '<p class="error">Tidak ada file poster yang diunggah.</p>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Film Baru - TomatoLite Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>TomatoLite Admin Dashboard</h1>
    </header>
    <nav>
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="dashboard.php">Admin Dashboard</a></li>
            <li><a href="../about.php">About</a></li>
            <li><a href="../logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
        </ul>
    </nav>
    <div class="container">
        <h2>Tambah Film Baru</h2>
        <?php echo $message; ?>
        <form action="add_film.php" method="POST" enctype="multipart/form-data">
            <label for="title">Judul Film:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>

            <label for="poster_file">Pilih Poster Film:</label>
            <input type="file" id="poster_file" name="poster_file" accept="image/*" required>

            <label for="description">Deskripsi:</label>
            <textarea id="description" name="description" rows="8" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>

            <label for="release_year">Tahun Rilis:</label>
            <input type="number" id="release_year" name="release_year" value="<?php echo htmlspecialchars($_POST['release_year'] ?? ''); ?>" min="1800" max="2100" required>
            
            <button type="submit">Tambah Film</button>
        </form>
    </div>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> TomatoLite. All rights reserved.</p>
    </footer>
    <script src="../js/script.js"></script>
</body>
</html>
<?php $conn->close(); ?>