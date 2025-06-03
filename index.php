<?php
session_start();
include 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TomatoLite - Sistem Review Film</title>
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
        <h2>Daftar Film</h2>
        <div class="film-grid">
            <?php
            // Corrected SQL: Only fetch necessary details, poster_url is no longer needed
            $sql = "SELECT id, title, description, release_year FROM films ORDER BY release_year DESC";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='film-item'>";
                    // Corrected Image Source: Points to get_image.php
                    echo "<img src='get_image.php?id=" . htmlspecialchars($row['id']) . "' alt='" . htmlspecialchars($row['title']) . "'>";
                    echo "<div class='film-info'>";
                    echo "<h3>" . htmlspecialchars($row['title']) . " (" . htmlspecialchars($row['release_year']) . ")</h3>";
                    echo "<h3>" . htmlspecialchars($row['title']) . " (" . htmlspecialchars($row['release_year']) . ")</h3>";
                    echo "<p>" . nl2br(substr(htmlspecialchars($row['description']), 0, 150)) . "...</p>";
                    echo "<a href='film.php?id=" . $row['id'] . "' class='view-details'>Lihat Detail & Review</a>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>Belum ada film yang tersedia.</p>";
            }
            ?>
        </div>
    </div>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> TomatoLite. All rights reserved.</p>
    </footer>
</body>
</html>
<?php $conn->close(); ?>