<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pageTitle = 'Check Application Status';

$message = '';
$messageType = '';
$application = null;

// Handle status check form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_status'])) {
    $referenceNo = sanitize($_POST['reference_no'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    
    $errors = [];
    
    // Validate inputs
    if (empty($referenceNo)) {
        $errors[] = 'Reference number is required';
    }
    
    if (empty($email) || !isValidEmail($email)) {
        $errors[] = 'Valid email address is required';
    }
    
    if (empty($errors)) {
        // Check if application exists
        $application = fetchOne(
            "SELECT * FROM applications WHERE reference_no = ? AND email = ?",
            [$referenceNo, $email]
        );
        
        if (!$application) {
            $message = 'No application found with the provided reference number and email.';
            $messageType = 'error';
        }
    } else {
        $message = implode('<br>', $errors);
        $messageType = 'error';
    }
}

require_once 'includes/header.php';
?>

<!-- Status Header -->
<section class="py-12 header-gradient text-white rounded-xl mb-12">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-6">Check Application Status</h1>
        <p class="text-xl max-w-3xl mx-auto">
            Track the progress of your application and get the latest updates on your admission status.
        </p>
    </div>
</section>

<div class="max-w-4xl mx-auto">
    <!-- Status Check Form -->
    <?php if (!$application): ?>
    <section class="bg-white rounded-lg shadow-md p-8 mb-12 animate-on-scroll">
        <h2 class="text-2xl font-bold text-dark-purple mb-6">Enter Your Application Details</h2>
        
        <?php if (!empty($message) && $messageType === 'error'): ?>
            <div class="mb-6 p-4 rounded-md bg-red-100 text-red-800">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <form action="status.php" method="post">
            <div class="mb-6">
                <label for="reference_no" class="block text-gray-700 font-medium mb-2">Application Reference Number</label>
                <input type="text" id="reference_no" name="reference_no" class="form-input" required>
                <p class="text-sm text-gray-600 mt-1">This is the reference number you received after submitting your application.</p>
            </div>
            
            <div class="mb-6">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" required>
                <p class="text-sm text-gray-600 mt-1">Enter the email address you used in your application.</p>
            </div>
            
            <button type="submit" name="check_status" class="btn-primary w-full">Check Status</button>
        </form>
    </section>
    
    <!-- Status Check Information -->
    <section class="bg-white rounded-lg shadow-md p-8 mb-12 animate-on-scroll">
        <h2 class="text-2xl font-bold text-dark-purple mb-6">Application Status Information</h2>
        
        <div class="space-y-6">
            <div>
                <h3 class="text-xl font-semibold text-medium-dark-purple mb-2">What does each status mean?</h3>
                <ul class="space-y-3 text-gray-700">
                    <li class="flex items-start space-x-3">
                        <span class="inline-block px-3 py-1 bg-yellow-200 text-yellow-800 rounded-full text-sm font-medium">Pending</span>
                        <span>Your application has been received and is waiting to be reviewed by our admissions team.</span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <span class="inline-block px-3 py-1 bg-blue-200 text-blue-800 rounded-full text-sm font-medium">Under Review</span>
                        <span>Your application is currently being reviewed by our admissions committee.</span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <span class="inline-block px-3 py-1 bg-green-200 text-green-800 rounded-full text-sm font-medium">Accepted</span>
                        <span>Congratulations! Your application has been approved.</span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <span class="inline-block px-3 py-1 bg-red-200 text-red-800 rounded-full text-sm font-medium">Rejected</span>
                        <span>Unfortunately, your application has not been approved at this time.</span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <span class="inline-block px-3 py-1 bg-gray-200 text-gray-800 rounded-full text-sm font-medium">Incomplete</span>
                        <span>Your application is missing required documents or information. Please check your email for details.</span>
                    </li>
                </ul>
            </div>
            
            <div>
                <h3 class="text-xl font-semibold text-medium-dark-purple mb-2">How long does the process take?</h3>
                <p class="text-gray-700">
                    The application review process typically takes 2-3 weeks from the date we receive your complete application package. 
                    During peak application periods, this process may take slightly longer. We appreciate your patience.
                </p>
            </div>
            
            <div>
                <h3 class="text-xl font-semibold text-medium-dark-purple mb-2">Need assistance?</h3>
                <p class="text-gray-700 mb-4">
                    If you have any questions about your application or need assistance, please contact our admissions team:
                </p>
                <div class="flex flex-col md:flex-row gap-4">
                    <a href="mailto:admissions@example.edu" class="flex items-center space-x-2 text-purple hover:text-dark-purple transition duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>admissions@example.edu</span>
                    </a>
                    <a href="tel:+15551234567" class="flex items-center space-x-2 text-purple hover:text-dark-purple transition duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span>+1 (555) 123-4567</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php else: ?>
    <!-- Application Status Display -->
    <section class="bg-white rounded-lg shadow-md overflow-hidden mb-12 animate-on-scroll">
        <div class="bg-gradient-to-r from-dark-purple to-purple py-6 px-8">
            <h2 class="text-2xl font-bold text-white">Application Status</h2>
        </div>
        
        <div class="p-8">
            <div class="mb-8">
                <h3 class="text-xl font-bold text-dark-purple mb-4">Status Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-600 mb-1">Reference Number</p>
                        <p class="font-bold text-gray-800"><?= htmlspecialchars($application['reference_no']) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600 mb-1">Application Date</p>
                        <p class="font-bold text-gray-800"><?= formatDate($application['created_at']) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600 mb-1">Full Name</p>
                        <p class="font-bold text-gray-800">
                            <?= htmlspecialchars($application['first_name'] . ' ' . ($application['middle_name'] ? $application['middle_name'] . ' ' : '') . $application['last_name']) ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600 mb-1">Email Address</p>
                        <p class="font-bold text-gray-800"><?= htmlspecialchars($application['email']) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600 mb-1">Program Applied For</p>
                        <p class="font-bold text-gray-800">
                            <?php
                            $program = getProgramById($application['program_id']);
                            echo htmlspecialchars($program['name']);
                            ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600 mb-1">Applicant Type</p>
                        <p class="font-bold text-gray-800"><?= ucfirst(htmlspecialchars($application['applicant_type'])) ?> Student</p>
                    </div>
                </div>
            </div>
            
            <div class="mb-8">
                <h3 class="text-xl font-bold text-dark-purple mb-4">Current Status</h3>
                
                <div class="flex flex-col md:flex-row md:items-center gap-6">
                    <div class="flex-1">
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
                        
                        <div class="<?= $statusClass ?> px-6 py-4 rounded-lg">
                            <h4 class="font-bold text-lg mb-2">Application Status: <?= $statusText ?></h4>
                            <p>
                                <?php
                                switch ($application['status']) {
                                    case 'pending':
                                        echo 'Your application has been received and is waiting to be reviewed by our admissions team.';
                                        break;
                                    case 'under_review':
                                        echo 'Your application is currently being reviewed by our admissions committee.';
                                        break;
                                    case 'accepted':
                                        echo 'Congratulations! Your application has been approved. Please check your email for next steps.';
                                        break;
                                    case 'rejected':
                                        echo 'We regret to inform you that your application has not been approved at this time.';
                                        break;
                                    case 'incomplete':
                                        echo 'Your application is missing required documents or information. Please check below for details.';
                                        break;
                                    default:
                                        echo 'Your application status is being processed.';
                                }
                                ?>
                            </p>
                            <?php if (!empty($application['notes'])): ?>
                                <div class="mt-3 pt-3 border-t border-dashed border-current">
                                    <p class="font-semibold">Additional Notes:</p>
                                    <p><?= nl2br(htmlspecialchars($application['notes'])) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="md:w-1/3">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-bold text-medium-dark-purple mb-3">Payment Status</h4>
                            <?php
                            $paymentStatusClasses = [
                                'unpaid' => 'bg-red-100 text-red-800',
                                'processing' => 'bg-yellow-100 text-yellow-800',
                                'paid' => 'bg-green-100 text-green-800',
                            ];
                            $paymentClass = $paymentStatusClasses[$application['payment_status']] ?? 'bg-gray-100 text-gray-800';
                            $paymentText = ucfirst($application['payment_status']);
                            ?>
                            <div class="<?= $paymentClass ?> px-3 py-2 rounded inline-block mb-2">
                                <?= $paymentText ?>
                            </div>
                            
                            <?php if ($application['payment_status'] === 'paid'): ?>
                                <div class="text-sm">
                                    <p><strong>Amount:</strong> PHP <?= number_format($application['payment_amount'], 2) ?></p>
                                    <p><strong>Date:</strong> <?= formatDate($application['payment_date']) ?></p>
                                    <p><strong>Reference:</strong> <?= htmlspecialchars($application['payment_reference']) ?></p>
                                </div>
                            <?php elseif ($application['payment_status'] === 'unpaid'): ?>
                                <p class="text-sm">Payment is required to process your application.</p>
                                <a href="payment.php?ref=<?= $application['reference_no'] ?>" class="btn-primary text-sm mt-2 inline-block">Pay Now</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if ($application['status'] === 'incomplete'): ?>
                <?php
                // Get missing documents
                $missingDocs = getMissingDocuments($application['id'], $application['applicant_type']);
                if (count($missingDocs) > 0):
                ?>
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-dark-purple mb-4">Missing Requirements</h3>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <p class="text-yellow-800 mb-3">The following documents are required to complete your application:</p>
                        <ul class="list-disc list-inside text-yellow-800">
                            <?php foreach ($missingDocs as $doc): ?>
                                <li><?= htmlspecialchars($doc['name']) ?> - <?= htmlspecialchars($doc['description']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="mt-4">
                            <a href="documents.php?ref=<?= $application['reference_no'] ?>" class="btn-primary text-sm">Upload Documents</a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php
            // Get application documents
            $documents = getApplicationDocuments($application['id']);
            if (count($documents) > 0):
            ?>
            <div class="mb-8">
                <h3 class="text-xl font-bold text-dark-purple mb-4">Submitted Documents</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($documents as $document): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($document['requirement_name']) ?></div>
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($document['original_name']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $docStatusClasses = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                        ];
                                        $docClass = $docStatusClasses[$document['status']] ?? 'bg-gray-100 text-gray-800';
                                        $docText = ucfirst($document['status']);
                                        ?>
                                        <span class="<?= $docClass ?> px-2 py-1 text-xs rounded"><?= $docText ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= formatDate($document['uploaded_at']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="status.php" class="text-purple hover:text-dark-purple transition duration-300">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Check Another Application
                    </span>
                </a>
                
                <div class="space-x-3">
                    <?php if ($application['status'] === 'accepted'): ?>
                        <a href="enrollment.php?ref=<?= $application['reference_no'] ?>" class="btn-primary">Proceed to Enrollment</a>
                    <?php endif; ?>
                    
                    <?php if ($application['status'] === 'incomplete'): ?>
                        <a href="documents.php?ref=<?= $application['reference_no'] ?>" class="btn-secondary">Upload Documents</a>
                    <?php endif; ?>
                    
                    <?php if ($application['payment_status'] === 'unpaid'): ?>
                        <a href="payment.php?ref=<?= $application['reference_no'] ?>" class="btn-primary">Pay Admission Fee</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>