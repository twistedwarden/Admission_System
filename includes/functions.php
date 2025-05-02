<?php
require_once 'config.php';
require_once 'db.php';

// Sanitize input 
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Validate email address
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate phone number (simple validation)
function isValidPhone($phone) {
    return preg_match('/^[0-9+\-\s]{8,15}$/', $phone);
}

// Validate date format (YYYY-MM-DD)
function isValidDate($date) {
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);
        return $dateTime && $dateTime->format('Y-m-d') === $date;
    }
    return false;
}

// Get all programs
function getPrograms() {
    return fetchAll("SELECT * FROM programs ORDER BY name ASC");
}

// Get program by ID
function getProgramById($id) {
    return fetchOne("SELECT * FROM programs WHERE id = ?", [$id]);
}

// Get requirements by applicant type
function getRequirementsByType($type) {
    return fetchAll(
        "SELECT * FROM requirements WHERE applicant_type = ? OR applicant_type = 'all' ORDER BY name ASC",
        [$type]
    );
}

// Get application by ID
function getApplicationById($id) {
    return fetchOne("SELECT * FROM applications WHERE id = ?", [$id]);
}

// Get application by reference number
function getApplicationByRefNo($refNo) {
    return fetchOne("SELECT * FROM applications WHERE reference_no = ?", [$refNo]);
}

// Get application documents
function getApplicationDocuments($applicationId) {
    return fetchAll(
        "SELECT d.*, r.name AS requirement_name, r.description 
         FROM documents d
         JOIN requirements r ON d.requirement_id = r.id 
         WHERE d.application_id = ?",
        [$applicationId]
    );
}

// Get missing documents for an application
function getMissingDocuments($applicationId, $applicantType) {
    $sql = "SELECT r.* FROM requirements r
            WHERE (r.applicant_type = ? OR r.applicant_type = 'all')
            AND r.required = 1
            AND r.id NOT IN (
                SELECT d.requirement_id FROM documents d 
                WHERE d.application_id = ?
            )";
    
    return fetchAll($sql, [$applicantType, $applicationId]);
}

// Create a new application
function createApplication($data) {
    $refNo = generateReferenceNumber();
    
    $sql = "INSERT INTO applications (
                reference_no, first_name, middle_name, last_name, email, 
                phone, address, birthdate, gender, program_id, 
                applicant_type, previous_school, previous_program, status
            ) VALUES (
                ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, 
                ?, ?, ?, ?
            )";
    
    $params = [
        $refNo, $data['first_name'], $data['middle_name'] ?? null, $data['last_name'], $data['email'],
        $data['phone'], $data['address'], $data['birthdate'], $data['gender'], $data['program_id'],
        $data['applicant_type'], $data['previous_school'] ?? null, $data['previous_program'] ?? null, STATUS_PENDING
    ];
    
    $id = insert($sql, $params);
    return $id ? ['id' => $id, 'reference_no' => $refNo] : false;
}

// Update application status
function updateApplicationStatus($id, $status, $adminId, $notes = null) {
    // Update application status
    $updated = update(
        "UPDATE applications SET status = ?, notes = ?, updated_at = NOW() WHERE id = ?",
        [$status, $notes, $id]
    );
    
    if ($updated) {
        // Add status update record
        insert(
            "INSERT INTO status_updates (application_id, status, notes, updated_by) VALUES (?, ?, ?, ?)",
            [$id, $status, $notes, $adminId]
        );
        
        return true;
    }
    
    return false;
}

// Upload and save a document
function saveDocument($applicationId, $requirementId, $file) {
    // Check if file exists and no errors
    if ($file['error'] !== UPLOAD_SUCCESS) {
        return false;
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFilename = uniqid() . '.' . $extension;
    $relativePath = 'uploads/' . $newFilename;
    $fullPath = UPLOAD_DIR . $newFilename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $fullPath)) {
        // Save document info to database
        $sql = "INSERT INTO documents (
                    application_id, requirement_id, file_name, original_name, 
                    file_path, file_size, mime_type, status
                ) VALUES (
                    ?, ?, ?, ?, 
                    ?, ?, ?, ?
                )";
        
        $params = [
            $applicationId, $requirementId, $newFilename, $file['name'],
            $relativePath, $file['size'], $file['type'], 'pending'
        ];
        
        return insert($sql, $params);
    }
    
    return false;
}

// Update payment status
function updatePaymentStatus($applicationId, $status, $amount = null, $reference = null) {
    $params = [$status, $applicationId];
    $sql = "UPDATE applications SET payment_status = ?";
    
    if ($status === PAYMENT_PAID && $amount && $reference) {
        $sql .= ", payment_amount = ?, payment_reference = ?, payment_date = NOW()";
        array_splice($params, 1, 0, [$amount, $reference]);
    }
    
    $sql .= " WHERE id = ?";
    
    return update($sql, $params);
}

// Get admin information by ID
function getAdminById($id) {
    return fetchOne("SELECT id, username, email, name FROM admin WHERE id = ?", [$id]);
}

// Validate admin login
function validateAdminLogin($username, $password) {
    $admin = fetchOne("SELECT * FROM admin WHERE username = ?", [$username]);
    
    if ($admin && password_verify($password, $admin['password'])) {
        // Remove password from session data
        unset($admin['password']);
        return $admin;
    }
    
    return false;
}

// Count applications by status
function countApplicationsByStatus() {
    return fetchOne("
        SELECT 
            COUNT(*) AS total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) AS under_review,
            SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) AS accepted,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected,
            SUM(CASE WHEN status = 'incomplete' THEN 1 ELSE 0 END) AS incomplete
        FROM applications
    ");
}

// Generate a strong random password
function generatePassword($length = 12) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
    return substr(str_shuffle($chars), 0, $length);
}

// Define file upload constants
define('UPLOAD_SUCCESS', UPLOAD_ERR_OK);

// Format date for display
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}
?>