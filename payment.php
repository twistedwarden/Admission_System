<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/email.php';

$pageTitle = 'Application Payment';

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

// Check if payment is already made
if ($application['payment_status'] === 'paid') {
    $message = 'Payment has already been completed for this application.';
    $messageType = 'info';
}

// Handle payment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_payment'])) {
    $amount = floatval($_POST['amount'] ?? 0);
    $method = sanitize($_POST['payment_method'] ?? '');
    $reference = sanitize($_POST['reference_number'] ?? '');
    
    $errors = [];
    
    // Validate inputs
    if ($amount != ADMISSION_FEE) {
        $errors[] = 'Payment amount must be PHP ' . ADMISSION_FEE;
    }
    
    if (empty($method)) {
        $errors[] = 'Payment method is required';
    }
    
    if (empty($reference)) {
        $errors[] = 'Payment reference number is required';
    }
    
    if (empty($errors)) {
        // In a real application, you would verify the payment with a payment gateway
        // For this demo, we'll assume the payment is valid
        
        // Update payment status
        if (updatePaymentStatus($application['id'], 'paid', $amount, $reference)) {
            // Update application object with payment details
            $application['payment_status'] = 'paid';
            $application['payment_amount'] = $amount;
            $application['payment_reference'] = $reference;
            $application['payment_date'] = date('Y-m-d H:i:s');
            
            // Send payment confirmation email
            sendPaymentConfirmationEmail($application);
            
            $message = 'Payment has been successfully processed. Thank you!';
            $messageType = 'success';
        } else {
            $message = 'An error occurred while processing your payment. Please try again.';
            $messageType = 'error';
        }
    } else {
        $message = implode('<br>', $errors);
        $messageType = 'error';
    }
}

require_once 'includes/header.php';
?>

<!-- Payment Header -->
<section class="py-12 header-gradient text-white rounded-xl mb-12">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-6">Application Fee Payment</h1>
        <p class="text-xl max-w-3xl mx-auto">
            Complete your application by paying the non-refundable application fee.
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
                <h3 class="text-lg font-bold text-dark-purple mb-2">Payment Status</h3>
                <?php
                $paymentStatusClasses = [
                    'unpaid' => 'bg-red-100 text-red-800',
                    'processing' => 'bg-yellow-100 text-yellow-800',
                    'paid' => 'bg-green-100 text-green-800',
                ];
                $paymentClass = $paymentStatusClasses[$application['payment_status']] ?? 'bg-gray-100 text-gray-800';
                $paymentText = ucfirst($application['payment_status']);
                ?>
                <p class="inline-block <?= $paymentClass ?> px-3 py-1 rounded-full text-sm">
                    <?= $paymentText ?>
                </p>
            </div>
        </div>
    </section>
    
    <?php if (!empty($message)): ?>
        <div class="mb-6 p-4 rounded-md <?= $messageType === 'success' || $messageType === 'info' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>
    
    <?php if ($application['payment_status'] === 'paid'): ?>
        <!-- Payment Details (if already paid) -->
        <section class="bg-white rounded-lg shadow-md p-8 mb-12">
            <h2 class="text-2xl font-bold text-dark-purple mb-6">Payment Details</h2>
            
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <p class="text-green-700 font-medium">Your payment has been successfully processed.</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-gray-600 font-medium mb-2">Amount Paid</h3>
                    <p class="text-xl font-bold text-dark-purple">PHP <?= number_format($application['payment_amount'], 2) ?></p>
                </div>
                
                <div>
                    <h3 class="text-gray-600 font-medium mb-2">Payment Date</h3>
                    <p class="text-xl font-bold text-dark-purple">
                        <?= date('F j, Y g:i A', strtotime($application['payment_date'])) ?>
                    </p>
                </div>
                
                <div>
                    <h3 class="text-gray-600 font-medium mb-2">Reference Number</h3>
                    <p class="text-xl font-bold text-dark-purple">
                        <?= htmlspecialchars($application['payment_reference']) ?>
                    </p>
                </div>
            </div>
            
            <div class="mt-8 flex justify-center">
                <a href="status.php?ref=<?= $application['reference_no'] ?>" class="btn-primary">Check Application Status</a>
            </div>
        </section>
    <?php else: ?>
        <!-- Payment Form -->
        <section class="bg-white rounded-lg shadow-md p-8 mb-12">
            <h2 class="text-2xl font-bold text-dark-purple mb-6">Payment Information</h2>
            
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-dark-purple">Application Fee</h3>
                    <p class="text-2xl font-bold text-dark-purple">PHP <?= number_format(ADMISSION_FEE, 2) ?></p>
                </div>
                <p class="text-gray-600">
                    This is a non-refundable fee for processing your application. Payment is required before 
                    your application can be reviewed by our admissions team.
                </p>
            </div>
            
            <form action="payment.php?ref=<?= $referenceNo ?>" method="post" id="payment-form">
                <input type="hidden" name="amount" value="<?= ADMISSION_FEE ?>">
                
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-dark-purple mb-4">Select Payment Method</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="payment-method border border-gray-200 rounded-lg p-4 cursor-pointer transition duration-300 hover:border-purple hover:bg-purple-50">
                            <input type="radio" name="payment_method" value="bank_transfer" class="hidden">
                            <div class="flex flex-col items-center">
                                <svg class="w-10 h-10 text-purple mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                </svg>
                                <span class="font-medium">Bank Transfer</span>
                            </div>
                        </label>
                        
                        <label class="payment-method border border-gray-200 rounded-lg p-4 cursor-pointer transition duration-300 hover:border-purple hover:bg-purple-50">
                            <input type="radio" name="payment_method" value="e_wallet" class="hidden">
                            <div class="flex flex-col items-center">
                                <svg class="w-10 h-10 text-purple mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium">E-Wallet</span>
                            </div>
                        </label>
                    </div>
                </div>
                
                <!-- Bank Transfer Details -->
                <div id="bank-transfer-details" class="payment-details hidden mb-6">
                    <h3 class="text-lg font-bold text-dark-purple mb-4">Bank Transfer Details</h3>
                    
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <p class="font-medium mb-2">Please transfer the exact amount to the following bank account:</p>
                        <ul class="space-y-2 text-gray-700">
                            <li><strong>Bank Name:</strong> University Bank</li>
                            <li><strong>Account Name:</strong> University Admissions</li>
                            <li><strong>Account Number:</strong> 1234-5678-9012</li>
                            <li><strong>Amount:</strong> PHP <?= number_format(ADMISSION_FEE, 2) ?></li>
                            <li><strong>Reference:</strong> <?= $application['reference_no'] ?></li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-gray-700 mb-2">After making the payment, please enter the transaction reference number:</p>
                        <label for="bank_reference" class="block text-gray-700 font-medium mb-2">Transaction Reference Number</label>
                        <input type="text" name="reference_number" id="bank_reference" class="form-input" required>
                    </div>
                </div>
                
                <!-- E-Wallet Payment -->
                <div id="e-wallet-details" class="payment-details hidden mb-6">
                    <h3 class="text-lg font-bold text-dark-purple mb-4">E-Wallet Payment</h3>
                    
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <p class="font-medium mb-2">Please send the payment to the following e-wallet accounts:</p>
                        
                        <div class="space-y-4">
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-bold text-medium-dark-purple">GCash</h4>
                                <p class="text-gray-700">Account Number: 0912-345-6789</p>
                                <p class="text-gray-700">Name: University Admissions</p>
                            </div>
                            
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-bold text-medium-dark-purple">PayMaya</h4>
                                <p class="text-gray-700">Account Number: 0912-345-6789</p>
                                <p class="text-gray-700">Name: University Admissions</p>
                            </div>
                        </div>
                        
                        <p class="mt-4 text-sm text-gray-600">
                            Be sure to include your reference number (<?= $application['reference_no'] ?>) in the payment description.
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-gray-700 mb-2">After making the payment, please enter the transaction reference number:</p>
                        <label for="ewallet_reference" class="block text-gray-700 font-medium mb-2">Transaction Reference Number</label>
                        <input type="text" name="reference_number" id="ewallet_reference" class="form-input" required>
                    </div>
                </div>
                
                <div id="payment-submit" class="mt-6 hidden">
                    <button type="submit" name="submit_payment" class="btn-primary w-full">Submit Payment</button>
                </div>
            </form>
        </section>
        
        <!-- Payment Guidelines -->
        <section class="bg-white rounded-lg shadow-md p-8 mb-12">
            <h2 class="text-2xl font-bold text-dark-purple mb-6">Payment Guidelines</h2>
            
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 mt-1 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="font-bold">Non-refundable Fee</h3>
                        <p class="text-gray-700">The application fee is non-refundable regardless of the admission decision.</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 mt-1 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="font-bold">Reference Number</h3>
                        <p class="text-gray-700">Always include your application reference number (<?= $application['reference_no'] ?>) in all payment transactions.</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 mt-1 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="font-bold">Exact Amount</h3>
                        <p class="text-gray-700">Pay the exact amount of PHP <?= number_format(ADMISSION_FEE, 2) ?>. Any excess or shortage may delay processing.</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 mt-1 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="font-bold">Payment Verification</h3>
                        <p class="text-gray-700">Payment verification may take 1-2 business days, after which your application status will be updated.</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 mt-1 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="font-bold">Payment Receipt</h3>
                        <p class="text-gray-700">Keep the payment receipt or transaction confirmation for your records.</p>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle payment method selection
        const paymentMethods = document.querySelectorAll('.payment-method input[type="radio"]');
        
        // Get reference to both detail sections and the submit button container
        const bankTransferDetails = document.getElementById('bank-transfer-details');
        const eWalletDetails = document.getElementById('e-wallet-details');
        const paymentSubmit = document.getElementById('payment-submit');
        
        // Reference fields
        const bankReferenceField = document.getElementById('bank_reference');
        const eWalletReferenceField = document.getElementById('ewallet_reference');
        
        // Function to reset reference fields
        function resetReferenceFields() {
            if (bankReferenceField) bankReferenceField.value = '';
            if (eWalletReferenceField) eWalletReferenceField.value = '';
        }
        
        // Function to handle reference field input
        function handleReferenceInput(activeField, inactiveField) {
            // When user types in the active field, copy the value to the hidden field
            activeField.addEventListener('input', function() {
                if (inactiveField) {
                    inactiveField.value = this.value;
                }
            });
        }
        
        // Set up reference field handlers
        if (bankReferenceField && eWalletReferenceField) {
            handleReferenceInput(bankReferenceField, eWalletReferenceField);
            handleReferenceInput(eWalletReferenceField, bankReferenceField);
        }
        
        paymentMethods.forEach(method => {
            method.addEventListener('change', function() {
                if (this.checked) {
                    // Hide all payment detail sections
                    bankTransferDetails.classList.add('hidden');
                    eWalletDetails.classList.add('hidden');
                    
                    resetReferenceFields();
                    
                    // Show selected payment method details
                    if (this.value === 'bank_transfer') {
                        bankTransferDetails.classList.remove('hidden');
                        bankReferenceField.required = true;
                        eWalletReferenceField.required = false;
                    } else if (this.value === 'e_wallet') {
                        eWalletDetails.classList.remove('hidden');
                        eWalletReferenceField.required = true;
                        bankReferenceField.required = false;
                    }
                    
                    // Show submit button
                    paymentSubmit.classList.remove('hidden');
                    
                    // Add active class to selected method
                    document.querySelectorAll('.payment-method').forEach(label => {
                        label.classList.remove('border-purple', 'bg-purple-50');
                    });
                    this.closest('.payment-method').classList.add('border-purple', 'bg-purple-50');
                }
            });
        });
        
        // Payment form validation
        const paymentForm = document.getElementById('payment-form');
        if (paymentForm) {
            paymentForm.addEventListener('submit', function(e) {
                const selectedMethod = document.querySelector('.payment-method input[type="radio"]:checked');
                
                if (!selectedMethod) {
                    e.preventDefault();
                    alert('Please select a payment method.');
                    return;
                }
                
                // Validate reference number based on selected payment method
                let referenceInput;
                if (selectedMethod.value === 'bank_transfer') {
                    referenceInput = document.getElementById('bank_reference');
                } else if (selectedMethod.value === 'e_wallet') {
                    referenceInput = document.getElementById('ewallet_reference');
                }
                
                if (referenceInput && !referenceInput.value.trim()) {
                    e.preventDefault();
                    alert('Please enter the transaction reference number.');
                    referenceInput.focus();
                    return;
                }
            });
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>
