<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$newPassword = 'admin123';
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$sql = "UPDATE admin SET password = ? WHERE username = 'admin'";
$result = update($sql, [$hashedPassword]);

if ($result) {
    echo "Password updated successfully!\n";
    echo "New password: " . $newPassword . "\n";
} else {
    echo "Failed to update password.\n";
}
?> 