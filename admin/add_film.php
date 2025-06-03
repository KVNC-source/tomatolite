<?php
session_start();
include '../includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $release_year = intval($_POST['release_year']);
    $poster_url = trim($_POST['poster_url']);

    if (empty($title) || empty($description) || empty($poster_url) || $release_year < 1800) { // Basic validation
        $message = '<p class="error">Semua field film harus diisi dengan benar.</p>';
    } else {
        $stmt = $conn->prepare("INSERT INTO films (title, description, release_year, poster_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $title, $description, $release_year, $poster_url);

        if ($stmt->execute()) {
            $message = '<p class="success">Film "' . htmlspecialchars($title) . '" berhasil ditambahkan!</p>';
            // Clear form fields after successful submission
            $_POST = array(); // Reset POST array to clear form values
        } else {
            $message = '<p class="error">Gagal menambahkan film: ' . $conn->error . '</p>';
        }
        $stmt->close();
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
        <form action="add_film.php" method="POST">
            <label for="title">Judul Film:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>

            <label for="description">Deskripsi:</label>
            <textarea id="description" name="description" rows="8" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>

            <label for="release_year">Tahun Rilis:</label>
            <input type="number" id="release_year" name="release_year" value="<?php echo htmlspecialchars($_POST['release_year'] ?? ''); ?>" min="1800" max="2100" required>

            <label for="poster_url">URL Poster Film:</label>
            <input type="text" id="poster_url" name="poster_url" value="<?php echo htmlspecialchars($_POST['poster_url'] ?? ''); ?>" required>

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