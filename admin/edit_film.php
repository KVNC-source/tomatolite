<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';
check_admin();

$message = '';
$film_id = null;
$film = [];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $film_id = $_GET['id'];
    $stmt_fetch = $conn->prepare("SELECT id, title, description, release_year, poster_url FROM films WHERE id = ?");
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
    $poster_url = trim($_POST['poster_url']);

    if (empty($title) || empty($description) || empty($poster_url) || $release_year < 1800) {
        $message = '<p class="error">Semua field film harus diisi dengan benar.</p>';
    } else {
        $stmt_update = $conn->prepare("UPDATE films SET title = ?, description = ?, release_year = ?, poster_url = ? WHERE id = ?");
        $stmt_update->bind_param("ssisi", $title, $description, $release_year, $poster_url, $film_id);

        if ($stmt_update->execute()) {
            $message = '<p class="success">Film "' . htmlspecialchars($title) . '" berhasil diperbarui!</p>';
            // Update film data in the form after successful update
            $film['title'] = $title;
            $film['description'] = $description;
            $film['release_year'] = $release_year;
            $film['poster_url'] = $poster_url;
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
            <form action="edit_film.php?id=<?php echo $film_id; ?>" method="POST">
                <label for="title">Judul Film:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($film['title']); ?>" required>

                <label for="description">Deskripsi:</label>
                <textarea id="description" name="description" rows="8" required><?php echo htmlspecialchars($film['description']); ?></textarea>

                <label for="release_year">Tahun Rilis:</label>
                <input type="number" id="release_year" name="release_year" value="<?php echo htmlspecialchars($film['release_year']); ?>" min="1800" max="2100" required>

                <label for="poster_url">URL Poster Film:</label>
                <input type="text" id="poster_url" name="poster_url" value="<?php echo htmlspecialchars($film['poster_url']); ?>" required>

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