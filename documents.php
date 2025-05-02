<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pageTitle = 'Upload Documents';

$message = '';
$messageType = '';
$application = null;

// Get reference number from URL or session
$referenceNo = isset($_GET['ref']) ? sanitize($_GET['ref']) : ($_SESSION['reference_no'] ?? '');

if (empty($referenceNo)) {
    header("Location: status.php");
    exit;
}

// Get application details
$application = getApplicationByRefNo($referenceNo);
if (!$application) {
    header("Location: status.php");
    exit;
}

// Get requirements for the applicant type
$requirements = getRequirementsByType($application['applicant_type']);

// Get already uploaded documents
$uploadedDocs = getApplicationDocuments($application['id']);

// Create an array of uploaded requirement IDs for easy lookup
$uploadedReqIds = [];
foreach ($uploadedDocs as $doc) {
    $uploadedReqIds[$doc['requirement_id']] = $doc;
}

// Handle document upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_documents'])) {
    $hasError = false;
    $uploadCount = 0;
    
    // Process each uploaded file
    foreach ($_FILES as $fileKey => $fileInfo) {
        // Check if it's a document upload field (starts with "document_")
        if (strpos($fileKey, 'document_') === 0) {
            $requirementId = intval(str_replace('document_', '', $fileKey));
            
            // Skip if no file was uploaded
            if ($fileInfo['error'] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            
            // Validate file
            if ($fileInfo['error'] !== UPLOAD_ERR_OK) {
                $message .= "Error uploading " . $fileInfo['name'] . ". ";
                $hasError = true;
                continue;
            }
            
            // Check file size (5MB max)
            if ($fileInfo['size'] > 5 * 1024 * 1024) {
                $message .= $fileInfo['name'] . " is too large. Maximum file size is 5MB. ";
                $hasError = true;
                continue;
            }
            
            // Check file type
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($fileInfo['type'], $allowedTypes)) {
                $message .= $fileInfo['name'] . " is not a valid file type. Please upload PDF, JPEG, or PNG files. ";
                $hasError = true;
                continue;
            }
            
            // Upload the file
            $result = saveDocument($application['id'], $requirementId, $fileInfo);
            if ($result) {
                $uploadCount++;
            } else {
                $message .= "Failed to save " . $fileInfo['name'] . ". ";
                $hasError = true;
            }
        }
    }
    
    if ($uploadCount > 0) {
        $message = $uploadCount . " document(s) uploaded successfully. ";
        $messageType = 'success';
        
        // Check if application status is incomplete, change it to pending if all required docs are uploaded
        if ($application['status'] === 'incomplete') {
            $missingDocs = getMissingDocuments($application['id'], $application['applicant_type']);
            if (count($missingDocs) === 0) {
                updateApplicationStatus($application['id'], 'pending', 1, 'All required documents have been uploaded.');
                $message .= "Your application status has been updated to Pending.";
                
                // Update application status in our variable
                $application['status'] = 'pending';
            }
        }
        
        // Refresh the uploaded documents list
        $uploadedDocs = getApplicationDocuments($application['id']);
        $uploadedReqIds = [];
        foreach ($uploadedDocs as $doc) {
            $uploadedReqIds[$doc['requirement_id']] = $doc;
        }
    } elseif ($hasError) {
        $messageType = 'error';
    } else {
        $message = "No documents were selected for upload.";
        $messageType = 'error';
    }
}

// Handle proceed to payment button
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proceed_to_payment'])) {
    header("Location: payment.php?ref=$referenceNo");
    exit;
}

require_once 'includes/header.php';
?>

<!-- Documents Header -->
<section class="py-12 header-gradient text-white rounded-xl mb-12">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-6">Upload Required Documents</h1>
        <p class="text-xl max-w-3xl mx-auto">
            Please upload all required documents for your application. Accepted file formats are PDF, JPEG, and PNG.
        </p>
    </div>
</section>

<div class="max-w-4xl mx-auto">
    <!-- Application Information -->
    <section class="bg-white rounded-lg shadow-md p-8 mb-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h3 class="text-lg font-bold text-dark-purple mb-2">Reference Number</h3>
                <p class="text-gray-700"><?= $application['reference_no'] ?></p>
            </div>
            
            <div>
                <h3 class="text-lg font-bold text-dark-purple mb-2">Applicant Name</h3>
                <p class="text-gray-700">
                    <?= htmlspecialchars($application['first_name'] . ' ' . $application['last_name']) ?>
                </p>
            </div>
            
            <div>
                <h3 class="text-lg font-bold text-dark-purple mb-2">Application Status</h3>
                <?php
                $statusColors = [
                    'pending' => 'yellow',
                    'under_review' => 'blue',
                    'accepted' => 'green',
                    'rejected' => 'red',
                    'incomplete' => 'gray',
                ];
                $statusColor = $statusColors[$application['status']] ?? 'gray';
                $statusText = ucwords(str_replace('_', ' ', $application['status']));
                ?>
                <p class="inline-block bg-<?= $statusColor ?>-100 text-<?= $statusColor ?>-800 px-3 py-1 rounded-full text-sm">
                    <?= $statusText ?>
                </p>
            </div>
        </div>
    </section>
    
    <?php if (!empty($message)): ?>
        <div class="mb-6 p-4 rounded-md <?= $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>
    
    <!-- Document Upload Form -->
    <section class="bg-white rounded-lg shadow-md p-8 mb-12">
        <h2 class="text-2xl font-bold text-dark-purple mb-6">Required Documents</h2>
        
        <form action="documents.php?ref=<?= $referenceNo ?>" method="post" enctype="multipart/form-data">
            <div class="space-y-6">
                <?php foreach ($requirements as $requirement): ?>
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-dark-purple">
                                    <?= htmlspecialchars($requirement['name']) ?>
                                    <?php if ($requirement['required']): ?>
                                        <span class="text-red-500">*</span>
                                    <?php else: ?>
                                        <span class="text-gray-500 text-sm">(Optional)</span>
                                    <?php endif; ?>
                                </h3>
                                <p class="text-gray-600 mt-1"><?= htmlspecialchars($requirement['description']) ?></p>
                            </div>
                            
                            <?php if (isset($uploadedReqIds[$requirement['id']])): ?>
                                <?php
                                $doc = $uploadedReqIds[$requirement['id']];
                                $docStatusClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                ];
                                $docClass = $docStatusClasses[$doc['status']] ?? 'bg-gray-100 text-gray-800';
                                $docText = ucfirst($doc['status']);
                                ?>
                                
                                <span class="<?= $docClass ?> px-3 py-1 rounded-full text-xs">
                                    <?= $docText ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (isset($uploadedReqIds[$requirement['id']])): ?>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium">Uploaded: <?= htmlspecialchars($uploadedReqIds[$requirement['id']]['original_name']) ?></p>
                                        <p class="text-sm text-gray-500">
                                            <?= date('F j, Y g:i A', strtotime($uploadedReqIds[$requirement['id']]['uploaded_at'])) ?>
                                        </p>
                                    </div>
                                    
                                    <a href="<?= $uploadedReqIds[$requirement['id']]['file_path'] ?>" target="_blank" class="text-purple hover:text-dark-purple transition duration-300">
                                        View Document
                                    </a>
                                </div>
                                
                                <?php if ($uploadedReqIds[$requirement['id']]['status'] === 'rejected'): ?>
                                    <div class="mt-4 flex items-center space-x-4">
                                        <span class="text-sm text-red-600">Upload a new document:</span>
                                        <input type="file" name="document_<?= $requirement['id'] ?>" class="text-sm" accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="mt-3">
                                <input type="file" name="document_<?= $requirement['id'] ?>" class="w-full" accept=".pdf,.jpg,.jpeg,.png">
                                <p class="text-xs text-gray-500 mt-1">Maximum file size: 5MB. Accepted formats: PDF, JPEG, PNG</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-8 flex flex-col md:flex-row md:justify-between">
                <button type="submit" name="upload_documents" class="btn-primary mb-4 md:mb-0">Upload Documents</button>
                
                <?php if ($application['payment_status'] === 'unpaid'): ?>
                    <button type="submit" name="proceed_to_payment" class="btn-secondary">Proceed to Payment</button>
                <?php else: ?>
                    <a href="status.php" class="btn-secondary text-center">Check Application Status</a>
                <?php endif; ?>
            </div>
        </form>
    </section>
    
    <!-- Document Guidelines -->
    <section class="bg-white rounded-lg shadow-md p-8 mb-12">
        <h2 class="text-2xl font-bold text-dark-purple mb-6">Document Guidelines</h2>
        
        <div class="space-y-4">
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 mt-1 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="font-bold">File Formats</h3>
                    <p class="text-gray-700">All documents must be in PDF, JPEG, or PNG format.</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 mt-1 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="font-bold">File Size</h3>
                    <p class="text-gray-700">Each file must be less than 5MB in size.</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 mt-1 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="font-bold">Document Quality</h3>
                    <p class="text-gray-700">Ensure all documents are clearly readable. Blurry or illegible documents may be rejected.</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 mt-1 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="font-bold">Document Verification</h3>
                    <p class="text-gray-700">All submitted documents will be verified by our admissions team. Falsified documents will result in application rejection.</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 mt-1 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="font-bold">Required Documents</h3>
                    <p class="text-gray-700">All documents marked with an asterisk (*) are required. Missing required documents will delay your application processing.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Next Steps -->
    <section class="bg-white rounded-lg shadow-md p-8 mb-12">
        <h2 class="text-2xl font-bold text-dark-purple mb-6">Next Steps</h2>
        
        <div class="space-y-4">
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 rounded-full bg-purple flex items-center justify-center text-white font-bold">1</div>
                <div>
                    <h3 class="font-bold text-lg">Upload Required Documents</h3>
                    <p class="text-gray-700">Ensure all required documents are uploaded and approved.</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 rounded-full bg-purple flex items-center justify-center text-white font-bold">2</div>
                <div>
                    <h3 class="font-bold text-lg">Pay Application Fee</h3>
                    <p class="text-gray-700">Complete the payment of the non-refundable application fee of PHP <?= ADMISSION_FEE ?>.</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 rounded-full bg-purple flex items-center justify-center text-white font-bold">3</div>
                <div>
                    <h3 class="font-bold text-lg">Application Review</h3>
                    <p class="text-gray-700">Our admissions team will review your application and documents. This process typically takes 2-3 weeks.</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 rounded-full bg-purple flex items-center justify-center text-white font-bold">4</div>
                <div>
                    <h3 class="font-bold text-lg">Decision Notification</h3>
                    <p class="text-gray-700">You will be notified of the admission decision via email. You can also check your application status online.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add file input change event to show selected filename
        const fileInputs = document.querySelectorAll('input[type="file"]');
        
        fileInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const fileName = this.files[0].name;
                    const fileSize = Math.round(this.files[0].size / 1024); // Convert to KB
                    
                    let parentDiv = this.parentElement;
                    let infoP = parentDiv.querySelector('.file-info');
                    
                    if (!infoP) {
                        infoP = document.createElement('p');
                        infoP.className = 'file-info text-sm mt-2';
                        parentDiv.appendChild(infoP);
                    }
                    
                    infoP.textContent = `Selected: ${fileName} (${fileSize} KB)`;
                }
            });
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>