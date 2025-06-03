<?php
$password_baru_anda = "tomatgacor"; 
$hashed_password_baru = password_hash($password_baru_anda, PASSWORD_DEFAULT);
echo "Hash password baru Anda untuk '" . $password_baru_anda . "':<br>";
echo "<strong>" . $hashed_password_baru . "</strong>";
?>