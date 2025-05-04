<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/email.php';

$pageTitle = 'View Application';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Get admin info
$admin = getAdminById($_SESSION['admin_id']);

// Check if application ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: applications.php");
    exit;
}

$applicationId = intval($_GET['id']);

// Get application details
$application = getApplicationById($applicationId);
if (!$application) {
    header("Location: applications.php");
    exit;
}

// Get program details
$program = getProgramById($application['program_id']);

// Get application documents
$documents = getApplicationDocuments($applicationId);

// Get missing documents
$missingDocs = getMissingDocuments($applicationId, $application['applicant_type']);

// Handle update status form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $newStatus = sanitize($_POST['status']);
    $notes = sanitize($_POST['notes'] ?? '');
    
    if (updateApplicationStatus($applicationId, $newStatus, $_SESSION['admin_id'], $notes)) {
        // Update application object
        $application['status'] = $newStatus;
        $application['notes'] = $notes;
        
        // Send email notification
        sendStatusUpdateEmail($application);
        
        $message = 'Application status updated successfully.';
        $messageType = 'success';
    } else {
        $message = 'Failed to update application status.';
        $messageType = 'error';
    }
}

// Handle document status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_document'])) {
    $documentId = intval($_POST['document_id']);
    $documentStatus = sanitize($_POST['document_status']);
    
    if (update(
        "UPDATE documents SET status = ? WHERE id = ? AND application_id = ?", 
        [$documentStatus, $documentId, $applicationId]
    )) {
        $message = 'Document status updated successfully.';
        $messageType = 'success';
        
        // Check if all required documents are approved
        $pendingDocs = fetchOne(
            "SELECT COUNT(*) as count 
             FROM documents d
             JOIN requirements r ON d.requirement_id = r.id
             WHERE d.application_id = ? AND r.required = 1 AND d.status != 'approved'",
            [$applicationId]
        );
        
        // If application is incomplete and all required docs are approved, auto-update to pending
        if ($application['status'] === 'incomplete' && $pendingDocs['count'] === 0) {
            updateApplicationStatus($applicationId, 'pending', $_SESSION['admin_id'], 'All required documents have been provided.');
            $application['status'] = 'pending'; // Update local status
            
            $message .= ' Application status changed to Pending as all required documents are now approved.';
        }
    } else {
        $message = 'Failed to update document status.';
        $messageType = 'error';
    }
}

// Handle payment status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_payment'])) {
    $paymentStatus = sanitize($_POST['payment_status']);
    $amount = null;
    $reference = null;
    
    if ($paymentStatus === 'paid') {
        $amount = floatval($_POST['payment_amount'] ?? 0);
        $reference = sanitize($_POST['payment_reference'] ?? '');
    }
    
    if (updatePaymentStatus($applicationId, $paymentStatus, $amount, $reference)) {
        $message = 'Payment status updated successfully.';
        $messageType = 'success';
        
        // Refresh application data
        $application = getApplicationById($applicationId);
        
        // Send payment confirmation email if status is paid
        if ($paymentStatus === 'paid') {
            sendPaymentConfirmationEmail($application);
        }
    } else {
        $message = 'Failed to update payment status.';
        $messageType = 'error';
    }
}

require_once 'admin_header.php';
?>

<!-- View Application Content -->
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="applications.php" class="text-purple hover:text-dark-purple transition duration-300 flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Applications
            </a>
            <h1 class="text-3xl font-bold text-dark-purple mt-2">Application Details</h1>
        </div>
        <div class="flex space-x-3">
            <a href="edit_application.php?id=<?= $applicationId ?>" class="btn-secondary flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                </svg>
                Edit Application
            </a>
        </div>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="mb-6 p-4 rounded-md <?= $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>
    
    <!-- Status and Reference -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h3 class="text-gray-500 font-medium mb-2">Reference Number</h3>
                <p class="text-xl font-bold text-dark-purple"><?= $application['reference_no'] ?></p>
            </div>
            
            <div>
                <h3 class="text-gray-500 font-medium mb-2">Application Date</h3>
                <p class="text-xl font-bold text-dark-purple"><?= date('F j, Y', strtotime($application['created_at'])) ?></p>
            </div>
            
            <div>
                <h3 class="text-gray-500 font-medium mb-2">Applicant Type</h3>
                <p class="text-xl font-bold text-dark-purple capitalize"><?= $application['applicant_type'] ?> Student</p>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Personal Information -->
        <div class="col-span-2 bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-dark-purple to-purple py-4 px-6">
                <h2 class="text-xl font-bold text-white">Personal Information</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-gray-500 font-medium mb-2">Full Name</h3>
                        <p class="text-lg font-semibold">
                            <?= htmlspecialchars($application['first_name'] . ' ' . ($application['middle_name'] ? $application['middle_name'] . ' ' : '') . $application['last_name']) ?>
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-gray-500 font-medium mb-2">Gender</h3>
                        <p class="text-lg font-semibold capitalize"><?= $application['gender'] ?></p>
                    </div>
                    
                    <div>
                        <h3 class="text-gray-500 font-medium mb-2">Date of Birth</h3>
                        <p class="text-lg font-semibold"><?= date('F j, Y', strtotime($application['birthdate'])) ?></p>
                    </div>
                    
                    <div>
                        <h3 class="text-gray-500 font-medium mb-2">Email Address</h3>
                        <p class="text-lg font-semibold"><?= htmlspecialchars($application['email']) ?></p>
                    </div>
                    
                    <div>
                        <h3 class="text-gray-500 font-medium mb-2">Phone Number</h3>
                        <p class="text-lg font-semibold"><?= htmlspecialchars($application['phone']) ?></p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <h3 class="text-gray-500 font-medium mb-2">Address</h3>
                        <p class="text-lg font-semibold"><?= nl2br(htmlspecialchars($application['address'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Current Status -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-dark-purple to-purple py-4 px-6">
                <h2 class="text-xl font-bold text-white">Current Status</h2>
            </div>
            
            <div class="p-6">
                <?php
                $statusClasses = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'under_review' => 'bg-blue-100 text-blue-800',
                    'accepted' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'incomplete' => 'bg-gray-100 text-gray-800',
                ];
                $statusClass = $statusClasses[$application['status']] ?? 'bg-gray-100 text-gray-800';
                $statusText = ucwords(str_replace('_', ' ', $application['status']));
                ?>
                
                <div class="<?= $statusClass ?> px-6 py-4 rounded-lg mb-6">
                    <h3 class="font-bold text-lg mb-2">Application Status: <?= $statusText ?></h3>
                    <?php if (!empty($application['notes'])): ?>
                        <div class="mt-2">
                            <p class="font-semibold">Notes:</p>
                            <p><?= nl2br(htmlspecialchars($application['notes'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <form action="view_application.php?id=<?= $applicationId ?>" method="post">
                    <div class="mb-4">
                        <label for="status" class="block text-gray-700 font-medium mb-2">Update Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="pending" <?= $application['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="under_review" <?= $application['status'] === 'under_review' ? 'selected' : '' ?>>Under Review</option>
                            <option value="accepted" <?= $application['status'] === 'accepted' ? 'selected' : '' ?>>Accepted</option>
                            <option value="rejected" <?= $application['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                            <option value="incomplete" <?= $application['status'] === 'incomplete' ? 'selected' : '' ?>>Incomplete</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="notes" class="block text-gray-700 font-medium mb-2">Notes</label>
                        <textarea id="notes" name="notes" rows="3" class="form-input"><?= htmlspecialchars($application['notes'] ?? '') ?></textarea>
                    </div>
                    
                    <button type="submit" name="update_status" class="btn-primary w-full">Update Status</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Program Information -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-dark-purple to-purple py-4 px-6">
                <h2 class="text-xl font-bold text-white">Program Information</h2>
            </div>
            
            <div class="p-6">
                <div class="mb-4">
                    <h3 class="text-gray-500 font-medium mb-2">Program Code</h3>
                    <p class="text-lg font-semibold"><?= htmlspecialchars($program['code']) ?></p>
                </div>
                <div class="mb-4">
                    <h3 class="text-gray-500 font-medium mb-2">Year Level</h3>
                    <p class="text-lg font-semibold"><?= htmlspecialchars($application['year_level']) ?></p>
                </div>
                <div class="mb-4">
                    <h3 class="text-gray-500 font-medium mb-2">Program Name</h3>
                    <p class="text-lg font-semibold"><?= htmlspecialchars($program['name']) ?></p>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-gray-500 font-medium mb-2">Department</h3>
                    <p class="text-lg font-semibold"><?= htmlspecialchars($program['department']) ?></p>
                </div>
                
                <div>
                    <h3 class="text-gray-500 font-medium mb-2">Description</h3>
                    <p><?= nl2br(htmlspecialchars($program['description'])) ?></p>
                </div>
            </div>
        </div>
        
        <!-- Previous School Information (for Transfer/Returning) -->
        <?php if ($application['applicant_type'] !== 'new' && (!empty($application['previous_school']) || !empty($application['previous_program']))): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-dark-purple to-purple py-4 px-6">
                    <h2 class="text-xl font-bold text-white">Previous Academic Information</h2>
                </div>
                
                <div class="p-6">
                    <?php if (!empty($application['previous_school'])): ?>
                        <div class="mb-4">
                            <h3 class="text-gray-500 font-medium mb-2">Previous Institution</h3>
                            <p class="text-lg font-semibold"><?= htmlspecialchars($application['previous_school']) ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($application['previous_program'])): ?>
                        <div>
                            <h3 class="text-gray-500 font-medium mb-2">Previous Program</h3>
                            <p class="text-lg font-semibold"><?= htmlspecialchars($application['previous_program']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Payment Information -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-dark-purple to-purple py-4 px-6">
                <h2 class="text-xl font-bold text-white">Payment Information</h2>
            </div>
            
            <div class="p-6">
                <?php
                $paymentStatusClasses = [
                    'unpaid' => 'bg-red-100 text-red-800',
                    'processing' => 'bg-yellow-100 text-yellow-800',
                    'paid' => 'bg-green-100 text-green-800',
                ];
                $paymentClass = $paymentStatusClasses[$application['payment_status']] ?? 'bg-gray-100 text-gray-800';
                $paymentText = ucfirst($application['payment_status']);
                ?>
                
                <div class="<?= $paymentClass ?> px-6 py-4 rounded-lg mb-6">
                    <h3 class="font-bold text-lg mb-2">Payment Status: <?= $paymentText ?></h3>
                    
                    <?php if ($application['payment_status'] === 'paid'): ?>
                        <div class="mt-2">
                            <p><strong>Amount:</strong> PHP <?= number_format($application['payment_amount'], 2) ?></p>
                            <p><strong>Date:</strong> <?= date('F j, Y', strtotime($application['payment_date'])) ?></p>
                            <p><strong>Reference:</strong> <?= htmlspecialchars($application['payment_reference']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <form action="view_application.php?id=<?= $applicationId ?>" method="post">
                    <div class="mb-4">
                        <label for="payment_status" class="block text-gray-700 font-medium mb-2">Update Payment Status</label>
                        <select id="payment_status" name="payment_status" class="form-select">
                            <option value="unpaid" <?= $application['payment_status'] === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                            <option value="processing" <?= $application['payment_status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="paid" <?= $application['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                        </select>
                    </div>
                    
                    <div id="payment-details" class="<?= $application['payment_status'] === 'paid' ? '' : 'hidden' ?>">
                        <div class="mb-4">
                            <label for="payment_amount" class="block text-gray-700 font-medium mb-2">Payment Amount (PHP)</label>
                            <input type="number" id="payment_amount" name="payment_amount" step="0.01" value="<?= $application['payment_amount'] ?? ADMISSION_FEE ?>" class="form-input">
                        </div>
                        
                        <div class="mb-4">
                            <label for="payment_reference" class="block text-gray-700 font-medium mb-2">Payment Reference</label>
                            <input type="text" id="payment_reference" name="payment_reference" value="<?= htmlspecialchars($application['payment_reference'] ?? '') ?>" class="form-input">
                        </div>
                    </div>
                    
                    <button type="submit" name="update_payment" class="btn-primary w-full">Update Payment</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Uploaded Documents -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-dark-purple to-purple py-4 px-6">
            <h2 class="text-xl font-bold text-white">Uploaded Documents</h2>
        </div>
        
        <div class="p-6">
            <?php if (count($documents) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 uppercase text-sm leading-normal">
                                <th class="table-header">Document</th>
                                <th class="table-header">Original Filename</th>
                                <th class="table-header">File Type</th>
                                <th class="table-header">Upload Date</th>
                                <th class="table-header">Status</th>
                                <th class="table-header">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm">
                            <?php foreach ($documents as $doc): ?>
                                <?php
                                $docStatusClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                ];
                                $docClass = $docStatusClasses[$doc['status']] ?? 'bg-gray-100 text-gray-800';
                                $docText = ucfirst($doc['status']);
                                ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="table-cell font-medium">
                                        <?= htmlspecialchars($doc['requirement_name']) ?>
                                    </td>
                                    <td class="table-cell">
                                        <?= htmlspecialchars($doc['original_name']) ?>
                                    </td>
                                    <td class="table-cell">
                                        <?= htmlspecialchars($doc['mime_type']) ?>
                                    </td>
                                    <td class="table-cell">
                                        <?= date('M d, Y', strtotime($doc['uploaded_at'])) ?>
                                    </td>
                                    <td class="table-cell">
                                        <span class="<?= $docClass ?> px-2 py-1 rounded text-xs">
                                            <?= $docText ?>
                                        </span>
                                    </td>
                                    <td class="table-cell">
                                        <div class="flex item-center">
                                            <a href="../<?= $doc['file_path'] ?>" target="_blank" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                            
                                            <button type="button" 
                                                    class="text-purple hover:text-dark-purple"
                                                    data-document-id="<?= $doc['id'] ?>"
                                                    data-document-name="<?= htmlspecialchars($doc['requirement_name']) ?>"
                                                    data-status="<?= $doc['status'] ?>"
                                                    onclick="openDocumentModal(this)">
                                                Update Status
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-gray-600">No documents have been uploaded yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Missing Documents (if any) -->
    <?php if (count($missingDocs) > 0): ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-red-600 to-red-400 py-4 px-6">
                <h2 class="text-xl font-bold text-white">Missing Documents</h2>
            </div>
            
            <div class="p-6">
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                    <p class="text-red-700">The following required documents are missing or have not been approved:</p>
                </div>
                
                <ul class="space-y-2">
                    <?php foreach ($missingDocs as $doc): ?>
                        <li class="flex items-start space-x-3">
                            <svg class="w-5 h-5 mt-0.5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>
                                <strong class="text-gray-800"><?= htmlspecialchars($doc['name']) ?></strong>
                                <p class="text-gray-600 text-sm"><?= htmlspecialchars($doc['description']) ?></p>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Document Status Modal -->
<div id="documentModal" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
        <div class="bg-gradient-to-r from-dark-purple to-purple py-4 px-6">
            <h3 class="text-xl font-bold text-white" id="modalTitle">Update Document Status</h3>
        </div>
        
        <form action="view_application.php?id=<?= $applicationId ?>" method="post">
            <input type="hidden" id="document_id" name="document_id" value="">
            
            <div class="p-6">
                <div class="mb-4">
                    <label for="document_status" class="block text-gray-700 font-medium mb-2">Status</label>
                    <select id="document_status" name="document_status" class="form-select">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" class="btn-secondary" onclick="closeDocumentModal()">Cancel</button>
                    <button type="submit" name="update_document" class="btn-primary">Update Status</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Payment status change
        const paymentStatus = document.getElementById('payment_status');
        const paymentDetails = document.getElementById('payment-details');
        
        paymentStatus.addEventListener('change', function() {
            if (this.value === 'paid') {
                paymentDetails.classList.remove('hidden');
            } else {
                paymentDetails.classList.add('hidden');
            }
        });
    });
    
    // Document modal functions
    function openDocumentModal(button) {
        const modal = document.getElementById('documentModal');
        const documentId = button.getAttribute('data-document-id');
        const documentName = button.getAttribute('data-document-name');
        const currentStatus = button.getAttribute('data-status');
        
        document.getElementById('document_id').value = documentId;
        document.getElementById('modalTitle').textContent = `Update Status: ${documentName}`;
        document.getElementById('document_status').value = currentStatus;
        
        modal.classList.remove('hidden');
    }
    
    function closeDocumentModal() {
        const modal = document.getElementById('documentModal');
        modal.classList.add('hidden');
    }
</script>

<?php require_once 'admin_footer.php'; ?>
