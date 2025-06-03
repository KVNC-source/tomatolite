<?php
session_start();
include '../includes/db.php';

$message = '';

// Handle delete film
if (isset($_GET['delete_film_id'])) {
    $film_id_to_delete = $_GET['delete_film_id'];
    $stmt_delete_film = $conn->prepare("DELETE FROM films WHERE id = ?");
    $stmt_delete_film->bind_param("i", $film_id_to_delete);
    if ($stmt_delete_film->execute()) {
        $message = '<p class="success">Film berhasil dihapus.</p>';
    } else {
        $message = '<p class="error">Gagal menghapus film: ' . $conn->error . '</p>';
    }
    $stmt_delete_film->close();
}

// Handle delete review
if (isset($_GET['delete_review_id'])) {
    $review_id_to_delete = $_GET['delete_review_id'];
    $stmt_delete_review = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt_delete_review->bind_param("i", $review_id_to_delete);
    if ($stmt_delete_review->execute()) {
        $message = '<p class="success">Review berhasil dihapus.</p>';
    } else {
        $message = '<p class="error">Gagal menghapus review: ' . $conn->error . '</p>';
    }
    $stmt_delete_review->close();
}

// Fetch all films for admin dashboard
$sql_films = "SELECT id, title, release_year FROM films ORDER BY id DESC";
$result_films = $conn->query($sql_films);
$films = [];
if ($result_films->num_rows > 0) {
    while($row = $result_films->fetch_assoc()) {
        $films[] = $row;
    }
}

// Fetch all reviews for admin dashboard
$sql_reviews = "SELECT r.id, r.comment, r.rating, r.created_at, u.username, f.title AS film_title
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                JOIN films f ON r.film_id = f.id
                ORDER BY r.created_at DESC";
$result_reviews = $conn->query($sql_reviews);
$reviews = [];
if ($result_reviews->num_rows > 0) {
    while($row = $result_reviews->fetch_assoc()) {
        $reviews[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TomatoLite</title>
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

        <div style="text-align: right; margin-bottom: 10px;">
    <a href="add_film.php" class="btn-tambah-atas">+ Tambah Film Baru</a>
</div>

        <h2>Manajemen Konten</h2>
        <?php echo $message; ?>

        <h3>Daftar Film</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Tahun Rilis</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($films)): ?>
                    <?php foreach ($films as $film): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($film['id']); ?></td>
                            <td><?php echo htmlspecialchars($film['title']); ?></td>
                            <td><?php echo htmlspecialchars($film['release_year']); ?></td>
                            <td class="actions">
                                <a href="edit_film.php?id=<?php echo $film['id']; ?>">Edit</a> |
                                <a href="dashboard.php?delete_film_id=<?php echo $film['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus film ini? Semua review terkait juga akan terhapus.')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Belum ada film.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h3>Daftar Ulasan Pengguna</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID Review</th>
                    <th>Film</th>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Komentar</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($review['id']); ?></td>
                            <td><?php echo htmlspecialchars($review['film_title']); ?></td>
                            <td><?php echo htmlspecialchars($review['username']); ?></td>
                            <td><?php echo htmlspecialchars($review['rating']); ?>/5</td>
                            <td><?php echo nl2br(htmlspecialchars(substr($review['comment'], 0, 100))) . (strlen($review['comment']) > 100 ? '...' : ''); ?></td>
                            <td><?php echo date("d M Y H:i", strtotime($review['created_at'])); ?></td>
                            <td class="actions">
                                <a href="dashboard.php?delete_review_id=<?php echo $review['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus ulasan ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">Belum ada ulasan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> TomatoLite. All rights reserved.</p>
    </footer>
</body>
</html>
<?php $conn->close(); ?>