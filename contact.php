<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pageTitle = 'Contact Us';

$message = '';
$messageType = '';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $inquiry = sanitize($_POST['inquiry'] ?? '');
    
    $errors = [];
    
    // Validate inputs
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($email) || !isValidEmail($email)) {
        $errors[] = 'Valid email is required';
    }
    
    if (empty($subject)) {
        $errors[] = 'Subject is required';
    }
    
    if (empty($inquiry)) {
        $errors[] = 'Inquiry is required';
    }
    
    if (empty($errors)) {
        // In a real application, this would send an email to the admin
        // For now, just show a success message
        $message = 'Thank you for your inquiry. We will get back to you soon!';
        $messageType = 'success';
        
        // Clear form fields
        $name = $email = $subject = $inquiry = '';
    } else {
        $message = implode('<br>', $errors);
        $messageType = 'error';
    }
}

require_once 'includes/header.php';
?>

<!-- Contact Header -->
<section class="py-12 header-gradient text-white rounded-xl mb-12">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-6">Contact Us</h1>
        <p class="text-xl max-w-3xl mx-auto">
            Have questions about our programs or the admission process? 
            We're here to help you every step of the way.
        </p>
    </div>
</section>

<!-- Contact Information -->
<section class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
    <!-- Contact Cards -->
    <div class="bg-white rounded-lg shadow-md p-8 transition duration-300 card-hover animate-on-scroll">
        <div class="w-16 h-16 rounded-full bg-slight-purple flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-dark-purple text-center mb-4">Call Us</h3>
        <p class="text-gray-700 text-center mb-2">General Inquiries:</p>
        <p class="text-dark-purple font-bold text-center mb-4">+1 (555) 123-4567</p>
        <p class="text-gray-700 text-center mb-2">Admissions Office:</p>
        <p class="text-dark-purple font-bold text-center">+1 (555) 987-6543</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-8 transition duration-300 card-hover animate-on-scroll">
        <div class="w-16 h-16 rounded-full bg-slight-purple flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-dark-purple text-center mb-4">Email Us</h3>
        <p class="text-gray-700 text-center mb-2">General Inquiries:</p>
        <p class="text-dark-purple font-bold text-center mb-4">info@example.edu</p>
        <p class="text-gray-700 text-center mb-2">Admissions Office:</p>
        <p class="text-dark-purple font-bold text-center">admissions@example.edu</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-8 transition duration-300 card-hover animate-on-scroll">
        <div class="w-16 h-16 rounded-full bg-slight-purple flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-dark-purple text-center mb-4">Visit Us</h3>
        <p class="text-gray-700 text-center mb-4">
            123 University Avenue<br>
            Metro City, State 12345<br>
            United States
        </p>
        <p class="text-gray-700 text-center mb-2">Office Hours:</p>
        <p class="text-dark-purple font-bold text-center">Mon-Fri: 8:00 AM - 5:00 PM</p>
    </div>
</section>

<!-- Contact Form and Map -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-16">
    <!-- Contact Form -->
    <div class="bg-white rounded-lg shadow-md p-8 animate-on-scroll">
        <h2 class="text-2xl font-bold text-dark-purple mb-6">Send Us a Message</h2>
        
        <?php if (!empty($message)): ?>
            <div class="mb-6 p-4 rounded-md <?= $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <form action="contact.php" method="post">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium mb-2">Full Name</label>
                <input type="text" id="name" name="name" value="<?= $name ?? '' ?>" class="form-input" required>
            </div>
            
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                <input type="email" id="email" name="email" value="<?= $email ?? '' ?>" class="form-input" required>
            </div>
            
            <div class="mb-4">
                <label for="subject" class="block text-gray-700 font-medium mb-2">Subject</label>
                <input type="text" id="subject" name="subject" value="<?= $subject ?? '' ?>" class="form-input" required>
            </div>
            
            <div class="mb-6">
                <label for="inquiry" class="block text-gray-700 font-medium mb-2">Your Inquiry</label>
                <textarea id="inquiry" name="inquiry" rows="5" class="form-input" required><?= $inquiry ?? '' ?></textarea>
            </div>
            
            <button type="submit" name="contact_submit" class="btn-primary w-full">Send Message</button>
        </form>
    </div>
    
    <!-- Map -->
    <div class="bg-white rounded-lg shadow-md p-8 animate-on-scroll">
        <h2 class="text-2xl font-bold text-dark-purple mb-6">Our Location</h2>
        <div class="h-[400px] bg-gray-200 rounded-md overflow-hidden">
            <!-- This would be a Google Maps embed in a real application -->
            <div class="w-full h-full bg-gradient-to-r from-slight-purple to-medium-dark-purple flex items-center justify-center">
                <div class="text-center text-white">
                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-xl font-bold">University Campus</p>
                    <p>123 University Avenue, Metro City</p>
                    <p class="mt-4 text-sm">Map loading placeholder - In a real application, this would be an interactive map.</p>
                </div>
            </div>
        </div>
        <div class="mt-6">
            <h3 class="text-lg font-bold text-dark-purple mb-2">Getting Here</h3>
            <ul class="text-gray-700 space-y-2">
                <li class="flex items-start space-x-2">
                    <svg class="w-5 h-5 mt-1 text-medium-dark-purple flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span><strong>By Car:</strong> Campus parking available in Lots A, B, and C</span>
                </li>
                <li class="flex items-start space-x-2">
                    <svg class="w-5 h-5 mt-1 text-medium-dark-purple flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span><strong>By Bus:</strong> Routes 10, 15, and 22 stop at University Station</span>
                </li>
                <li class="flex items-start space-x-2">
                    <svg class="w-5 h-5 mt-1 text-medium-dark-purple flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span><strong>By Train:</strong> Metro City Station is a 10-minute walk from campus</span>
                </li>
            </ul>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="mb-16">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-dark-purple mb-6">Frequently Asked Questions</h2>
        
        <div class="space-y-4">
            <div class="border border-gray-200 rounded-md">
                <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                    <span>What are the office hours for the Admissions Office?</span>
                    <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden p-4 pt-0 text-gray-700">
                    <p>The Admissions Office is open Monday through Friday from 8:00 AM to 5:00 PM. We are closed on weekends and holidays.</p>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-md">
                <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                    <span>How can I check the status of my application?</span>
                    <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden p-4 pt-0 text-gray-700">
                    <p>You can check the status of your application at any time by visiting our <a href="status.php" class="text-purple hover:text-dark-purple">Status Check page</a> and entering your reference number.</p>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-md">
                <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                    <span>How long does it take to review an application?</span>
                    <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden p-4 pt-0 text-gray-700">
                    <p>The review process typically takes 2-3 weeks from the date we receive your complete application package, including all required documents and payment.</p>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-md">
                <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                    <span>What should I do if I'm having technical issues with my application?</span>
                    <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden p-4 pt-0 text-gray-700">
                    <p>If you're experiencing technical issues with your application, please contact our support team at <strong>support@example.edu</strong> or call <strong>+1 (555) 123-4567</strong>. Please include a description of the issue and any error messages you received.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // FAQ toggles
        const faqToggles = document.querySelectorAll('.faq-toggle');
        
        faqToggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                const content = toggle.nextElementSibling;
                const arrow = toggle.querySelector('svg');
                
                content.classList.toggle('hidden');
                arrow.classList.toggle('rotate-180');
            });
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>