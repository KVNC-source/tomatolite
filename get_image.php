<?php
// get_image.php
include 'includes/db.php'; // Include your database connection

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $film_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT poster_image, poster_mime_type FROM films WHERE id = ?");
    $stmt->bind_param("i", $film_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // This is the critical part
        header("Content-Type: " . $row['poster_mime_type']);
        echo $row['poster_image']; // This should output the binary image data
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "Image not found.";
    }
    $stmt->close();
} else {
    header("HTTP/1.0 400 Bad Request");
    echo "Invalid request.";
}
$conn->close();
?>