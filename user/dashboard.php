<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';
check_user(); // Pastikan hanya user yang bisa akses

$message = '';
$user_id = $_SESSION['user_id'];

// Handle delete review (user bisa hapus review sendiri)
if (isset($_GET['delete_review_id'])) {
    $review_id_to_delete = $_GET['delete_review_id'];
    // Pastikan review ini milik user yang login
    $stmt_check = $conn->prepare("SELECT id FROM reviews WHERE id = ? AND user_id = ?");
    $stmt_check->bind_param("ii", $review_id_to_delete, $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows > 0) {
        $stmt_delete = $conn->prepare("DELETE FROM reviews WHERE id = ? AND user_id = ?");
        $stmt_delete->bind_param("ii", $review_id_to_delete, $user_id);
        if ($stmt_delete->execute()) {
            $message = '<p class="success">Review berhasil dihapus.</p>';
        } else {
            $message = '<p class="error">Gagal menghapus review.</p>';
        }
        $stmt_delete->close();
    } else {
        $message = '<p class="error">Review tidak ditemukan atau bukan milik Anda.</p>';
    }
    $stmt_check->close();
}

// Fetch user's reviews
$sql_user_reviews = "SELECT r.id, r.comment, r.rating, r.created_at, f.title AS film_title, f.id AS film_id
                     FROM reviews r
                     JOIN films f ON r.film_id = f.id
                     WHERE r.user_id = ?
                     ORDER BY r.created_at DESC";
$stmt_user_reviews = $conn->prepare($sql_user_reviews);
$stmt_user_reviews->bind_param("i", $user_id);
$stmt_user_reviews->execute();
$result_user_reviews = $stmt_user_reviews->get_result();
$user_reviews = [];
if ($result_user_reviews->num_rows > 0) {
    while($row = $result_user_reviews->fetch_assoc()) {
        $user_reviews[] = $row;
    }
}
$stmt_user_reviews->close();

// Count user stats
$stmt_count = $conn->prepare("SELECT COUNT(*) as review_count FROM reviews WHERE user_id = ?");
$stmt_count->bind_param("i", $user_id);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$user_stats = $result_count->fetch_assoc();
$stmt_count->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - TomatoLite</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>TomatoLite User Dashboard</h1>
    </header>
    <nav>
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="../about.php">About</a></li>
            <li><a href="../logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
        </ul>
    </nav>
    <div class="container">
        <h2>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <?php echo $message; ?>

        <!-- User Stats -->
        <div class="user-stats" style="background: #f4f4f4; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <h3>Statistik Anda</h3>
            <p><strong>Total Review:</strong> <?php echo $user_stats['review_count']; ?></p>
        </div>

        <!-- User's Reviews -->
        <h3>Review Anda</h3>
        <?php if (!empty($user_reviews)): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Film</th>
                        <th>Rating</th>
                        <th>Komentar</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($user_reviews as $review): ?>
                        <tr>
                            <td>
                                <a href="../film.php?id=<?php echo $review['film_id']; ?>">
                                    <?php echo htmlspecialchars($review['film_title']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($review['rating']); ?>/5</td>
                            <td><?php echo nl2br(htmlspecialchars(substr($review['comment'], 0, 100))) . (strlen($review['comment']) > 100 ? '...' : ''); ?></td>
                            <td><?php echo date("d M Y H:i", strtotime($review['created_at'])); ?></td>
                            <td class="actions">
                                <a href="dashboard.php?delete_review_id=<?php echo $review['id']; ?>" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus review ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Anda belum memberikan review pada film apapun.</p>
            <p><a href="../film.php">Lihat film dan berikan review pertama Anda!</a></p>
        <?php endif; ?>
        </div>
    </div>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> TomatoLite. All rights reserved.</p>
    </footer>
</body>
</html>
<?php $conn->close(); ?>