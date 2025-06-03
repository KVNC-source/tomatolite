<?php
session_start();
include 'includes/db.php';


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$film_id = $_GET['id'];

// Fetch film details
$stmt_film = $conn->prepare("SELECT id, title, description, release_year, poster_url FROM films WHERE id = ?");
$stmt_film->bind_param("i", $film_id);
$stmt_film->execute();
$result_film = $stmt_film->get_result();

if ($result_film->num_rows === 0) {
    echo "<p>Film tidak ditemukan.</p>";
    $conn->close();
    exit();
}
$film = $result_film->fetch_assoc();
$stmt_film->close();

$message = '';

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['user_id'])) {
        $message = '<p class="error">Anda harus login untuk memberikan review.</p>';
    } else {
        $user_id = $_SESSION['user_id'];
        $rating = intval($_POST['rating']);
        $comment = trim($_POST['comment']);

        if ($rating >= 1 && $rating <= 5 && !empty($comment)) {
            // Check if user already reviewed this film
            $check_review_stmt = $conn->prepare("SELECT id FROM reviews WHERE film_id = ? AND user_id = ?");
            $check_review_stmt->bind_param("ii", $film_id, $user_id);
            $check_review_stmt->execute();
            $check_review_result = $check_review_stmt->get_result();

            if ($check_review_result->num_rows > 0) {
                $message = '<p class="error">Anda sudah memberikan review untuk film ini.</p>';
            } else {
                $stmt_insert_review = $conn->prepare("INSERT INTO reviews (film_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
                $stmt_insert_review->bind_param("iiss", $film_id, $user_id, $rating, $comment);

                if ($stmt_insert_review->execute()) {
                    $message = '<p class="success">Review berhasil ditambahkan!</p>';
                } else {
                    $message = '<p class="error">Gagal menambahkan review: ' . $conn->error . '</p>';
                }
                $stmt_insert_review->close();
            }
            $check_review_stmt->close();
        } else {
            $message = '<p class="error">Rating harus antara 1-5 dan komentar tidak boleh kosong.</p>';
        }
    }
}

// Fetch reviews for the film
$stmt_reviews = $conn->prepare("SELECT r.rating, r.comment, r.created_at, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.film_id = ? ORDER BY r.created_at DESC");
$stmt_reviews->bind_param("i", $film_id);
$stmt_reviews->execute();
$result_reviews = $stmt_reviews->get_result();
$reviews = [];
while ($row = $result_reviews->fetch_assoc()) {
    $reviews[] = $row;
}
$stmt_reviews->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($film['title']); ?> - TomatoLite</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>TomatoLite</h1>
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
        <div class="film-detail">
            <img src="<?php echo htmlspecialchars($film['poster_url']); ?>" alt="<?php echo htmlspecialchars($film['title']); ?>">
            <div class="film-content">
                <h2><?php echo htmlspecialchars($film['title']); ?> (<?php echo htmlspecialchars($film['release_year']); ?>)</h2>
                <p><?php echo nl2br(htmlspecialchars($film['description'])); ?></p>
            </div>
        </div>

        <div class="reviews-section">
            <h3>Ulasan Pengguna</h3>
            <?php echo $message; // Display messages ?>

            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="review-form">
                <h4>Berikan Ulasan Anda:</h4>
                <form action="film.php?id=<?php echo $film_id; ?>" method="POST">
                    <label for="rating">Rating (1-5):</label>
                    <input type="number" id="rating" name="rating" min="1" max="5" required>

                    <label for="comment">Komentar:</label>
                    <textarea id="comment" name="comment" rows="5" required></textarea>

                    <button type="submit" name="submit_review">Kirim Ulasan</button>
                </form>
            </div>
            <?php else: ?>
                <p>Silakan <a href="login.php">login</a> untuk memberikan ulasan.</p>
            <?php endif; ?>

            <div class="existing-reviews">
                <?php if (count($reviews) > 0): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-item">
                            <strong><?php echo htmlspecialchars($review['username']); ?></strong> - Rating: <?php echo htmlspecialchars($review['rating']); ?>/5<br>
                            <small><?php echo date("d M Y H:i", strtotime($review['created_at'])); ?></small>
                            <p><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Belum ada ulasan untuk film ini.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> TomatoLite. All rights reserved.</p>
    </footer>
    <script src="js/script.js"></script>
</body>
</html>
<?php $conn->close(); ?>