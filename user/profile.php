<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';
check_user();

$message = '';
$user_id = $_SESSION['user_id'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username']);
    $new_password = trim($_POST['password']);
    
    if (!empty($new_username)) {
        // Check if username already exists (exclude current user)
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt_check->bind_param("si", $new_username, $user_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            $message = '<p class="error">Username sudah digunakan.</p>';
        } else {
            // Update username
            $stmt_update = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt_update->bind_param("si", $new_username, $user_id);
            if ($stmt_update->execute()) {
                $_SESSION['username'] = $new_username;
                $message = '<p class="success">Username berhasil diubah.</p>';
            } else {
                $message = '<p class="error">Gagal mengubah username.</p>';
            }
            $stmt_update->close();
        }
        $stmt_check->close();
    }
    
    // Update password if provided
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt_pass = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt_pass->bind_param("si", $hashed_password, $user_id);
        if ($stmt_pass->execute()) {
            $message .= '<p class="success">Password berhasil diubah.</p>';
        } else {
            $message .= '<p class="error">Gagal mengubah password.</p>';
        }
        $stmt_pass->close();
    }
}

// Get current user data
$stmt_user = $conn->prepare("SELECT username, created_at FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_data = $result_user->fetch_assoc();
$stmt_user->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - TomatoLite</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>TomatoLite - Profile</h1>
    </header>
    <nav>
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="../film.php">Film</a></li>
            <li><a href="../about.php">About</a></li>
            <li><a href="../logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
        </ul>
    </nav>
    <div class="container">
        <h2>Profile Pengguna</h2>
        <?php echo $message; ?>
        
        <div style="background: #f4f4f4; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3>Informasi Akun</h3>
            <p><strong>Username saat ini:</strong> <?php echo htmlspecialchars($user_data['username']); ?></p>
            <p><strong>Bergabung sejak:</strong> <?php echo date("d M Y", strtotime($user_data['created_at'])); ?></p>
        </div>

        <h3>Edit Profile</h3>
        <form action="profile.php" method="POST">
            <label for="username">Username Baru:</label>
            <input type="text" id="username" name="username" 
                   value="<?php echo htmlspecialchars($user_data['username']); ?>" 
                   placeholder="Masukkan username baru">
            
            <label for="password">Password Baru (kosongkan jika tidak ingin mengubah):</label>
            <input type="password" id="password" name="password" 
                   placeholder="Masukkan password baru">
            
            <button type="submit">Update Profile</button>
        </form>
        
        <div style="margin-top: 30px;">
            <a href="dashboard.php">← Kembali ke Dashboard</a>
        </div>
    </div>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> TomatoLite. All rights reserved.</p>
    </footer>
</body>
</html>
<?php $conn->close(); ?>