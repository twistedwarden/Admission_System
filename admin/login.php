<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

$pageTitle = 'Admin Login';

$message = '';
$messageType = '';

// Check if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $errors = [];
    
    // Validate inputs
    if (empty($username)) {
        $errors[] = 'Username is required';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    
    if (empty($errors)) {
        // Validate admin credentials
        $admin = validateAdminLogin($username, $password);
        
        if ($admin) {
            // Set admin session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_username'] = $admin['username'];
            
            // Redirect to dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            $message = 'Invalid username or password';
            $messageType = 'error';
        }
    } else {
        $message = implode('<br>', $errors);
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - <?= SITE_NAME ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'light-purple': '#D3B3F2',
                        'slight-purple': '#C394F2',
                        'purple': '#8243D9',
                        'medium-dark-purple': '#5B26A6',
                        'dark-purple': '#3B1273',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen flex flex-col items-center justify-center bg-[#D3B3F2] font-sans text-gray-800">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-xl">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-dark-purple mb-2">Admin Login</h1>
            <p class="text-gray-600">Enter your credentials to access the admin dashboard</p>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="mb-6 p-4 rounded-md <?= $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <form action="login.php" method="post">
            <div class="mb-6">
                <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
                <input type="text" id="username" name="username" class="w-full rounded-md border-2 border-gray-300 px-4 py-3 focus:border-purple focus:outline-none focus:ring-2 focus:ring-purple/50 transition duration-200" required>
            </div>
            
            <div class="mb-8">
                <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                <input type="password" id="password" name="password" class="w-full rounded-md border-2 border-gray-300 px-4 py-3 focus:border-purple focus:outline-none focus:ring-2 focus:ring-purple/50 transition duration-200" required>
            </div>
            
            <button type="submit" name="login" class="w-full bg-medium-dark-purple text-white px-6 py-3 rounded-md font-medium hover:bg-dark-purple transition duration-300">Log In</button>
        </form>
        
        <div class="mt-6 text-center">
            <a href="../index.php" class="text-purple hover:text-dark-purple transition duration-300">
                &larr; Back to Main Site
            </a>
        </div>
    </div>
    
    <div class="mt-8 text-center text-sm text-gray-600">
        <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
    </div>
</body>
</html>