<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';
check_admin();

$message = '';
$film_id = null;
$film = [];

// Temporarily enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $film_id = $_GET['id'];
    // UPDATED: Select poster_image and poster_mime_type instead of poster_url
    $stmt_fetch = $conn->prepare("SELECT id, title, description, release_year, poster_image, poster_mime_type FROM films WHERE id = ?");
    $stmt_fetch->bind_param("i", $film_id);
    $stmt_fetch->execute();
    $result_fetch = $stmt_fetch->get_result();

    if ($result_fetch->num_rows > 0) {
        $film = $result_fetch->fetch_assoc();
    } else {
        $message = '<p class="error">Film tidak ditemukan.</p>';
    }
    $stmt_fetch->close();
} else {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $film_id !== null) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $release_year = intval($_POST['release_year']);

    $update_image = false;
    $file_content = null;
    $file_type = null;

    // Check if a new file was uploaded
    if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['poster_file']['tmp_name'];
        $file_type = $_FILES['poster_file']['type'];
        $file_content = file_get_contents($file_tmp_name);

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file_type, $allowed_types)) {
            $message = '<p class="error">Tipe file tidak didukung. Hanya gambar JPEG, PNG, GIF yang diizinkan.</p>';
        } else {
            $update_image = true;
        }
    } else if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Specific error message for upload issues other than no file selected
        switch ($_FILES['poster_file']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $message = '<p class="error">Ukuran file poster terlalu besar. Periksa pengaturan upload_max_filesize dan post_max_size di php.ini.</p>';
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = '<p class="error">File poster hanya terunggah sebagian.</p>';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = '<p class="error">Folder sementara untuk upload poster tidak ditemukan.</p>';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = '<p class="error">Gagal menulis file poster ke disk.</p>';
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = '<p class="error">Ekstensi PHP menghentikan unggahan file poster.</p>';
                break;
            default:
                $message = '<p class="error">Gagal mengunggah file poster karena alasan yang tidak diketahui.</p>';
                break;
        }
    }


    if (empty($title) || empty($description) || $release_year < 1800) {
        $message = '<p class="error">Semua field film harus diisi dengan benar.</p>';
    } elseif ($update_image === false && !isset($film['poster_image'])) {
        // If no new image uploaded and no existing image, it means a poster is required
        $message = '<p class="error">Poster film harus diunggah.</p>';
    } else {
        $sql_update = "UPDATE films SET title = ?, description = ?, release_year = ?";
        $param_types = "ssi";
        $bind_params = [&$title, &$description, &$release_year]; // Pass by reference

        if ($update_image) {
            $sql_update .= ", poster_image = ?, poster_mime_type = ?";
            $param_types .= "bs";
            $bind_params[] = &$file_content; // Pass by reference for BLOB
            $bind_params[] = &$file_type;
        }

        $sql_update .= " WHERE id = ?";
        $param_types .= "i";
        $bind_params[] = &$film_id;

        $stmt_update = $conn->prepare($sql_update);

        // Call bind_param dynamically
        call_user_func_array([$stmt_update, 'bind_param'], array_merge([$param_types], $bind_params));

        if ($stmt_update->execute()) {
            $message = '<p class="success">Film "' . htmlspecialchars($title) . '" berhasil diperbarui!</p>';
            // Update film data in the form after successful update for current view
            $film['title'] = $title;
            $film['description'] = $description;
            $film['release_year'] = $release_year;
            // If image was updated, the next fetch/display will use the new one via get_image.php
        } else {
            $message = '<p class="error">Gagal memperbarui film: ' . $conn->error . '</p>';
        }
        $stmt_update->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Film - TomatoLite Admin</title>
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
            <li><a href="add_film.php">Tambah Film Baru</a></li>
            <li><a href="../logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
        </ul>
    </nav>
    <div class="container">
        <h2>Edit Film</h2>
        <?php echo $message; ?>
        <?php if (!empty($film)): ?>
            <form action="edit_film.php?id=<?php echo $film_id; ?>" method="POST" enctype="multipart/form-data">
                <label for="title">Judul Film:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($film['title']); ?>" required>

                <label for="poster_file">Pilih Poster Film (kosongkan jika tidak ingin mengubah):</label>
                <?php if (isset($film['poster_image']) && !empty($film['poster_image'])): ?>
                    <div style="margin-bottom: 10px;">
                        <p>Poster Saat Ini:</p>
                        <img src="../get_image.php?id=<?php echo htmlspecialchars($film['id']); ?>" alt="Current Poster" style="max-width: 150px; height: auto; border: 1px solid #ddd;">
                    </div>
                    <input type="file" id="poster_file" name="poster_file" accept="image/*">
                <?php else: ?>
                    <input type="file" id="poster_file" name="poster_file" accept="image/*" required>
                <?php endif; ?>

                <label for="description">Deskripsi:</label>
                <textarea id="description" name="description" rows="8" required><?php echo htmlspecialchars($film['description']); ?></textarea>

                <label for="release_year">Tahun Rilis:</label>
                <input type="number" id="release_year" name="release_year" value="<?php echo htmlspecialchars($film['release_year']); ?>" min="1800" max="2100" required>

                <button type="submit">Update Film</button>
            </form>
        <?php else: ?>
            <p>Silakan kembali ke <a href="dashboard.php">dashboard</a> untuk memilih film yang akan diedit.</p>
        <?php endif; ?>
    </div>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> TomatoLite. All rights reserved.</p>
    </footer>
    <script src="../js/script.js"></script>
</body>
</html>
<?php $conn->close(); ?>