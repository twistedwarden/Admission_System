<?php
// Database connection
$host = 'localhost';
$dbname = 'admission_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper function to execute queries
function executeQuery($query, $params = []) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        // Log error
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

// Get a single row
function fetchOne($query, $params = []) {
    $stmt = executeQuery($query, $params);
    return $stmt ? $stmt->fetch() : false;
}

// Get multiple rows
function fetchAll($query, $params = []) {
    $stmt = executeQuery($query, $params);
    return $stmt ? $stmt->fetchAll() : false;
}

// Insert data and return last insert ID
function insert($query, $params = []) {
    global $pdo;
    
    $stmt = executeQuery($query, $params);
    if ($stmt) {
        return $pdo->lastInsertId();
    }
    return false;
}

// Update data and return number of affected rows
function update($query, $params = []) {
    $stmt = executeQuery($query, $params);
    return $stmt ? $stmt->rowCount() : false;
}

// Delete data and return number of affected rows
function delete($query, $params = []) {
    $stmt = executeQuery($query, $params);
    return $stmt ? $stmt->rowCount() : false;
}

// Generate a unique reference number
function generateReferenceNumber() {
    $prefix = date('Ymd');
    $random = sprintf('%04d', mt_rand(0, 9999));
    $refNo = $prefix . $random;
    
    // Check if reference number already exists
    $exists = fetchOne("SELECT id FROM applications WHERE reference_no = ?", [$refNo]);
    if ($exists) {
        return generateReferenceNumber(); // Try again if exists
    }
    
    return $refNo;
}
?>