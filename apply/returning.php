<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

$pageTitle = 'Returning Student Application';

$message = '';
$messageType = '';
$formData = [];

// Get all programs
$programs = getPrograms();

// Get requirements for returning students
$requirements = getRequirementsByType(APPLICANT_RETURNING);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_application'])) {
    // Get form data
    $formData = [
        'first_name' => sanitize($_POST['first_name'] ?? ''),
        'middle_name' => sanitize($_POST['middle_name'] ?? ''),
        'last_name' => sanitize($_POST['last_name'] ?? ''),
        'email' => sanitize($_POST['email'] ?? ''),
        'phone' => sanitize($_POST['phone'] ?? ''),
        'address' => sanitize($_POST['address'] ?? ''),
        'birthdate' => sanitize($_POST['birthdate'] ?? ''),
        'gender' => sanitize($_POST['gender'] ?? ''),
        'program_id' => intval($_POST['program_id'] ?? 0),
        'year_level' => sanitize($_POST['year_level'] ?? ''),
        'previous_school' => sanitize($_POST['previous_school'] ?? ''),  // Same school, but for tracking
        'previous_program' => sanitize($_POST['previous_program'] ?? ''),
        'applicant_type' => APPLICANT_RETURNING,
    ];
    
    $errors = [];
    
    // Validate required fields
    if (empty($formData['first_name'])) {
        $errors[] = 'First name is required';
    }
    
    if (empty($formData['last_name'])) {
        $errors[] = 'Last name is required';
    }
    
    if (empty($formData['email']) || !isValidEmail($formData['email'])) {
        $errors[] = 'Valid email address is required';
    }
    
    if (empty($formData['phone']) || !isValidPhone($formData['phone'])) {
        $errors[] = 'Valid phone number is required';
    }
    
    if (empty($formData['address'])) {
        $errors[] = 'Address is required';
    }
    
    if (empty($formData['birthdate']) || !isValidDate($formData['birthdate'])) {
        $errors[] = 'Valid birthdate is required (YYYY-MM-DD)';
    }
    
    if (empty($formData['gender']) || !in_array($formData['gender'], ['male', 'female', 'other'])) {
        $errors[] = 'Gender is required';
    }
    
    if ($formData['program_id'] <= 0) {
        $errors[] = 'Program selection is required';
    }
    
    if (empty($formData['year_level']) || !in_array($formData['year_level'], ['1st Year', '2nd Year', '3rd Year', '4th Year'])) {
        $errors[] = 'Year level is required';
    }
    
    if (empty($formData['previous_program'])) {
        $errors[] = 'Previous program is required';
    }
    
    // If no errors, create application
    if (empty($errors)) {
        $result = createApplication($formData);
        
        if ($result) {
            // Create session for document upload
            $_SESSION['application_id'] = $result['id'];
            $_SESSION['reference_no'] = $result['reference_no'];
            
            // Redirect to document upload page
            header("Location: ../documents.php?ref={$result['reference_no']}");
            exit;
        } else {
            $message = 'An error occurred while creating your application. Please try again.';
            $messageType = 'error';
        }
    } else {
        $message = implode('<br>', $errors);
        $messageType = 'error';
    }
}

// Pre-select program if provided in URL
$selectedProgram = 0;
if (isset($_GET['program'])) {
    $selectedProgram = intval($_GET['program']);
}

require_once '../includes/header.php';
?>

<!-- Application Header -->
<section class="py-12 header-gradient text-white rounded-xl mb-12">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-6">Returning Student Application</h1>
        <p class="text-xl max-w-3xl mx-auto">
            Welcome back! Complete the form below to apply as a returning student.
        </p>
    </div>
</section>

<!-- Application Form -->
<section class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-8 mb-12 animate-on-scroll">
    <?php if (!empty($message)): ?>
        <div class="mb-6 p-4 rounded-md <?= $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>
    
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-dark-purple mb-4">Personal Information</h2>
        <p class="text-gray-700 mb-6">
            Please provide your personal details as they appear on your official documents.
        </p>
        
        <form action="returning.php" method="post" id="application-form">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-gray-700 font-medium mb-2">First Name *</label>
                    <input type="text" id="first_name" name="first_name" value="<?= $formData['first_name'] ?? '' ?>" class="form-input" required>
                </div>
                
                <div>
                    <label for="middle_name" class="block text-gray-700 font-medium mb-2">Middle Name (if applicable)</label>
                    <input type="text" id="middle_name" name="middle_name" value="<?= $formData['middle_name'] ?? '' ?>" class="form-input">
                </div>
                
                <div>
                    <label for="last_name" class="block text-gray-700 font-medium mb-2">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" value="<?= $formData['last_name'] ?? '' ?>" class="form-input" required>
                </div>
                
                <div>
                    <label for="gender" class="block text-gray-700 font-medium mb-2">Gender *</label>
                    <select id="gender" name="gender" class="form-select" required>
                        <option value="" disabled <?= empty($formData['gender'] ?? '') ? 'selected' : '' ?>>Select Gender</option>
                        <option value="male" <?= ($formData['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= ($formData['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                        <option value="other" <?= ($formData['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                
                <div>
                    <label for="birthdate" class="block text-gray-700 font-medium mb-2">Date of Birth *</label>
                    <input type="date" id="birthdate" name="birthdate" value="<?= $formData['birthdate'] ?? '' ?>" class="form-input" required>
                </div>
                
                <div>
                    <label for="email" class="block text-gray-700 font-medium mb-2">Email Address *</label>
                    <input type="email" id="email" name="email" value="<?= $formData['email'] ?? '' ?>" class="form-input" required>
                </div>
                
                <div>
                    <label for="phone" class="block text-gray-700 font-medium mb-2">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" value="<?= $formData['phone'] ?? '' ?>" class="form-input" required>
                </div>
                
                <div class="md:col-span-2">
                    <label for="address" class="block text-gray-700 font-medium mb-2">Complete Address *</label>
                    <textarea id="address" name="address" rows="3" class="form-input" required><?= $formData['address'] ?? '' ?></textarea>
                </div>
            </div>
            
            <div class="mt-8 mb-8">
                <h2 class="text-2xl font-bold text-dark-purple mb-4">Previous Academic Information</h2>
                <p class="text-gray-700 mb-6">
                    Provide information about your previous enrollment at our institution.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="previous_school" class="block text-gray-700 font-medium mb-2">Institution Name *</label>
                        <input type="text" id="previous_school" name="previous_school" value="Our Institution" class="form-input bg-gray-100" readonly>
                        <p class="text-sm text-gray-600 mt-1">As a returning student, this refers to our institution.</p>
                    </div>
                    
                    <div>
                        <label for="previous_program" class="block text-gray-700 font-medium mb-2">Previous Program *</label>
                        <select id="previous_program" name="previous_program" class="form-select" required>
                            <option value="" disabled <?= empty($formData['previous_program'] ?? '') ? 'selected' : '' ?>>Select Previous Program</option>
                            <?php foreach ($programs as $program): ?>
                                <option value="<?= htmlspecialchars($program['name']) ?>" <?= ($formData['previous_program'] ?? '') === $program['name'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($program['code'] . ' - ' . $program['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="year_level" class="block text-gray-700 font-medium mb-2">Year Level *</label>
                        <select id="year_level" name="year_level" class="form-select" required>
                            <option value="" disabled <?= empty($formData['year_level'] ?? '') ? 'selected' : '' ?>>Select Year Level</option>
                            <option value="1st Year" <?= ($formData['year_level'] ?? '') === '1st Year' ? 'selected' : '' ?>>1st Year</option>
                            <option value="2nd Year" <?= ($formData['year_level'] ?? '') === '2nd Year' ? 'selected' : '' ?>>2nd Year</option>
                            <option value="3rd Year" <?= ($formData['year_level'] ?? '') === '3rd Year' ? 'selected' : '' ?>>3rd Year</option>
                            <option value="4th Year" <?= ($formData['year_level'] ?? '') === '4th Year' ? 'selected' : '' ?>>4th Year</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 mb-8">
                <h2 class="text-2xl font-bold text-dark-purple mb-4">Program Selection</h2>
                <p class="text-gray-700 mb-6">
                    Choose the academic program you wish to enroll in this time.
                </p>
                
                <div>
                    <label for="program_id" class="block text-gray-700 font-medium mb-2">Select Program *</label>
                    <select id="program_id" name="program_id" class="form-select" required>
                        <option value="" disabled <?= $selectedProgram === 0 && empty($formData['program_id'] ?? '') ? 'selected' : '' ?>>Select Program</option>
                        <?php foreach ($programs as $program): ?>
                            <option value="<?= $program['id'] ?>" <?= ($selectedProgram === $program['id'] || ($formData['program_id'] ?? 0) === $program['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($program['code'] . ' - ' . $program['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="mt-8 mb-8">
                <h2 class="text-2xl font-bold text-dark-purple mb-4">Required Documents</h2>
                <p class="text-gray-700 mb-6">
                    The following documents will be required after submitting your application:
                </p>
                
                <div class="bg-gray-50 rounded-lg p-6">
                    <ul class="space-y-3">
                        <?php foreach ($requirements as $requirement): ?>
                            <li class="flex items-start space-x-3">
                                <?php if ($requirement['required']): ?>
                                    <svg class="w-5 h-5 mt-0.5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <span>
                                        <strong class="text-gray-800"><?= htmlspecialchars($requirement['name']) ?></strong> (Required)
                                        <p class="text-gray-600 text-sm"><?= htmlspecialchars($requirement['description']) ?></p>
                                    </span>
                                <?php else: ?>
                                    <svg class="w-5 h-5 mt-0.5 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>
                                        <strong class="text-gray-800"><?= htmlspecialchars($requirement['name']) ?></strong> (Optional)
                                        <p class="text-gray-600 text-sm"><?= htmlspecialchars($requirement['description']) ?></p>
                                    </span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="mt-4 text-sm text-gray-600">
                        You will be able to upload these documents in the next step.
                    </p>
                </div>
            </div>
            
            <div class="mt-8">
                <h2 class="text-2xl font-bold text-dark-purple mb-4">Terms & Privacy</h2>
                
                <div class="mb-6">
                    <label class="flex items-start space-x-3">
                        <input type="checkbox" id="terms_agreement" class="form-checkbox mt-1" required>
                        <span class="text-gray-700">
                            I agree to the <a href="#" class="text-purple hover:text-dark-purple">Terms and Conditions</a> and <a href="#" class="text-purple hover:text-dark-purple">Privacy Policy</a>. I confirm that all information provided is accurate and complete.
                        </span>
                    </label>
                </div>
                
                <div class="mb-6">
                    <label class="flex items-start space-x-3">
                        <input type="checkbox" id="data_consent" class="form-checkbox mt-1" required>
                        <span class="text-gray-700">
                            I consent to the collection, storage, and processing of my personal data for admission purposes and communications related to my application.
                        </span>
                    </label>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end">
                <button type="submit" name="submit_application" class="btn-primary">Submit Application</button>
            </div>
        </form>
    </div>
</section>

<!-- Additional Information -->
<section class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-8 mb-12">
    <h2 class="text-2xl font-bold text-dark-purple mb-6">Returning Student Information</h2>
    
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-medium-dark-purple mb-4">Benefits for Returning Students</h3>
        <ul class="space-y-3 text-gray-700">
            <li class="flex items-start space-x-3">
                <svg class="w-5 h-5 mt-1 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span><strong>Simplified Application Process</strong> - Returning students enjoy a streamlined application with fewer document requirements.</span>
            </li>
            <li class="flex items-start space-x-3">
                <svg class="w-5 h-5 mt-1 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span><strong>Credit Transfer</strong> - Previously completed courses may be credited towards your new program.</span>
            </li>
            <li class="flex items-start space-x-3">
                <svg class="w-5 h-5 mt-1 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span><strong>Priority Processing</strong> - Applications from returning students are given priority in the review process.</span>
            </li>
            <li class="flex items-start space-x-3">
                <svg class="w-5 h-5 mt-1 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span><strong>Returning Student Orientation</strong> - Special orientation sessions designed to help you transition back to academic life.</span>
            </li>
        </ul>
    </div>
    
    <div class="space-y-6">
        <div class="flex items-start space-x-4">
            <div class="w-10 h-10 rounded-full bg-slight-purple flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold">1</span>
            </div>
            <div>
                <h3 class="text-xl font-semibold text-dark-purple mb-2">Complete Application Form</h3>
                <p class="text-gray-700">
                    Fill out the returning student application form with your updated information.
                </p>
            </div>
        </div>
        
        <div class="flex items-start space-x-4">
            <div class="w-10 h-10 rounded-full bg-slight-purple flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold">2</span>
            </div>
            <div>
                <h3 class="text-xl font-semibold text-dark-purple mb-2">Upload Required Documents</h3>
                <p class="text-gray-700">
                    Submit your previous transcript and any other required documents.
                </p>
            </div>
        </div>
        
        <div class="flex items-start space-x-4">
            <div class="w-10 h-10 rounded-full bg-slight-purple flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold">3</span>
            </div>
            <div>
                <h3 class="text-xl font-semibold text-dark-purple mb-2">Pay Application Fee</h3>
                <p class="text-gray-700">
                    Pay the reduced returning student application fee of PHP <?= ADMISSION_FEE ?>.
                </p>
            </div>
        </div>
        
        <div class="flex items-start space-x-4">
            <div class="w-10 h-10 rounded-full bg-slight-purple flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold">4</span>
            </div>
            <div>
                <h3 class="text-xl font-semibold text-dark-purple mb-2">Credit Evaluation</h3>
                <p class="text-gray-700">
                    If you're changing programs, your previous credits will be evaluated for transfer to the new program.
                </p>
            </div>
        </div>
        
        <div class="flex items-start space-x-4">
            <div class="w-10 h-10 rounded-full bg-slight-purple flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold">5</span>
            </div>
            <div>
                <h3 class="text-xl font-semibold text-dark-purple mb-2">Acceptance & Enrollment</h3>
                <p class="text-gray-700">
                    Upon approval, you'll receive an acceptance letter and information about enrollment for the upcoming semester.
                </p>
            </div>
        </div>
    </div>
    
    <div class="mt-8 p-4 bg-gray-50 rounded-lg">
        <p class="text-sm text-gray-600">
            <strong>Note:</strong> If you've been away for more than 5 years, additional documentation may be required. 
            Contact the Admissions Office if you have any questions about your returning student status.
        </p>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('application-form');
        
        form.addEventListener('submit', function(e) {
            const termsCheckbox = document.getElementById('terms_agreement');
            const dataCheckbox = document.getElementById('data_consent');
            
            if (!termsCheckbox.checked || !dataCheckbox.checked) {
                e.preventDefault();
                alert('You must agree to the terms and privacy policy to continue.');
            }
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>
