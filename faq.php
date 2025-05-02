<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pageTitle = 'FAQ';

require_once 'includes/header.php';
?>

<!-- FAQ Header -->
<section class="py-12 header-gradient text-white rounded-xl mb-12">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-6">Frequently Asked Questions</h1>
        <p class="text-xl max-w-3xl mx-auto">
            Find answers to common questions about our admission process, requirements, and more.
        </p>
    </div>
</section>

<!-- Search Box -->
<section class="mb-12">
    <div class="max-w-xl mx-auto">
        <div class="relative">
            <input type="text" id="faq-search" placeholder="Search FAQs..." class="form-input pl-12">
            <svg class="w-6 h-6 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>
</section>

<!-- FAQ Categories -->
<section class="mb-12">
    <div class="flex flex-wrap justify-center gap-4">
        <button class="faq-category-btn bg-white px-6 py-3 rounded-full shadow-md text-dark-purple font-medium hover:bg-slight-purple hover:text-white transition duration-300 focus:outline-none active" data-category="all">
            All FAQs
        </button>
        <button class="faq-category-btn bg-white px-6 py-3 rounded-full shadow-md text-dark-purple font-medium hover:bg-slight-purple hover:text-white transition duration-300 focus:outline-none" data-category="application">
            Application Process
        </button>
        <button class="faq-category-btn bg-white px-6 py-3 rounded-full shadow-md text-dark-purple font-medium hover:bg-slight-purple hover:text-white transition duration-300 focus:outline-none" data-category="requirements">
            Requirements
        </button>
        <button class="faq-category-btn bg-white px-6 py-3 rounded-full shadow-md text-dark-purple font-medium hover:bg-slight-purple hover:text-white transition duration-300 focus:outline-none" data-category="fees">
            Fees & Payment
        </button>
        <button class="faq-category-btn bg-white px-6 py-3 rounded-full shadow-md text-dark-purple font-medium hover:bg-slight-purple hover:text-white transition duration-300 focus:outline-none" data-category="other">
            Other Questions
        </button>
    </div>
</section>

<!-- FAQ Content -->
<section class="mb-16">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div id="faq-container" class="space-y-6">
            <!-- Application Process FAQs -->
            <div class="faq-category" data-category="application">
                <h2 class="text-2xl font-bold text-dark-purple mb-6">Application Process</h2>
                
                <div class="space-y-4">
                    <div class="faq-item border border-gray-200 rounded-md">
                        <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                            <span>How do I apply for admission?</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden p-4 pt-0 text-gray-700">
                            <p>You can apply for admission by visiting our "Apply Now" page and selecting the appropriate application form based on your status (new student, returning student, or transfer student). Fill out the form, upload the required documents, and submit your application fee.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item border border-gray-200 rounded-md">
                        <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                            <span>What is the application deadline?</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden p-4 pt-0 text-gray-700">
                            <p>Application deadlines vary by semester:</p>
                            <ul class="list-disc list-inside mt-2">
                                <li>Fall Semester: June 30</li>
                                <li>Spring Semester: November 30</li>
                                <li>Summer Semester: April 15</li>
                            </ul>
                            <p class="mt-2">We recommend applying early as programs may fill up before the deadline.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item border border-gray-200 rounded-md">
                        <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                            <span>How long does the application review process take?</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden p-4 pt-0 text-gray-700">
                            <p>The application review process typically takes 2-3 weeks from the date we receive your complete application package, including all required documents and payment. During peak application periods, the process may take slightly longer.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item border border-gray-200 rounded-md">
                        <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                            <span>Can I apply for multiple programs?</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden p-4 pt-0 text-gray-700">
                            <p>Yes, you can apply for up to three programs in a single application. You will need to rank them in order of preference. If you are not accepted into your first-choice program, your application will automatically be considered for your second and third choices.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item border border-gray-200 rounded-md">
                        <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                            <span>How will I be notified of the admission decision?</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden p-4 pt-0 text-gray-700">
                            <p>You will be notified of the admission decision via email. You can also check the status of your application at any time by visiting our Status Check page and entering your reference number.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Requirements FAQs -->
            <div class="faq-category" data-category="requirements">
                <h2 class="text-2xl font-bold text-dark-purple mb-6">Requirements</h2>
                
                <div class="space-y-4">
                    <div class="faq-item border border-gray-200 rounded-md">
                        <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                            <span>What documents are required for a new student application?</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden p-4 pt-0 text-gray-700">
                            <p>New student applicants must submit the following documents:</p>
                            <ul class="list-disc list-inside mt-2">
                                <li>Completed application form</li>
                                <li>High school transcript of records</li>
                                <li>Birth certificate (NSO/PSA authenticated)</li>
                                <li>2x2 recent colored photo with white background</li>
                                <li>Medical certificate from licensed physician</li>
                                <li>Proof of application fee payment</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="faq-item border border-gray-200 rounded-md">
                        <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                            <span>What additional documents are required for transfer students?</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden p-4 pt-0 text-gray-700">
                            <p>In addition to the standard requirements, transfer students must also submit:</p>
                            <ul class="list-disc list-inside mt-2">
                                <li>Honorable dismissal from previous college/university</li>
                                <li>Transcript of records from previous college/university</li>
                                <li>Valid ID from previous school</li>
                                <li>Course descriptions for credit evaluation (optional)</li>
                                <li>Recommendation letter (optional but recommended)</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="faq-item border border-gray-200 rounded-md">
                        <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                            <span>What are the document format requirements for uploads?</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden p-4 pt-0 text-gray-700">
                            <p>All documents must be uploaded in PDF, JPG, or PNG format. The maximum file size for each document is 5MB. Please ensure that all documents are clear, legible, and complete.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item border border-gray-200 rounded-md">
                        <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                            <span>Do international students have different requirements?</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden p-4 pt-0 text-gray-700">
                            <p>Yes, international students have additional requirements, including:</p>
                            <ul class="list-disc list-inside mt-2">
                                <li>Passport copy</li>
                                <li>Visa documentation</li>
                                <li>English proficiency test results (TOEFL, IELTS, etc.)</li>
                                <li>Foreign credential evaluation for academic records</li>
                                <li>Financial documentation proving ability to pay for education</li>
                            </ul>
                            <p class="mt-2">Please contact the International Admissions Office for more specific information.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Fees & Payment FAQs -->
            <div class="faq-category" data-category="fees">
                <h2 class="text-2xl font-bold text-dark-purple mb-6">Fees & Payment</h2>
                
                <div class="space-y-4">
                    <div class="faq-item border border-gray-200 rounded-md">
                        <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                            <span>What is the application fee?</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden p-4 pt-0 text-gray-700">
                            <p>The application fee is PHP 500. This fee is non-refundable and must be paid before your application can be processed.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item border border-gray-200 rounded-md">
                        <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                            <span>What payment methods are accepted?</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden p-4 pt-0 text-gray-700">
                            <p>We accept the following payment methods:</p>
                            <ul class="list-disc list-inside mt-2">
                                <li>Online payment via credit/debit card</li>
                                <li>Bank transfer</li>
                                <li>Mobile payment apps (GCash, PayMaya)</li>
                                <li>Over-the-counter payment at our cashier's office</li>
                            </ul>
                            <p class="mt-2">Please note that payment processing times may vary depending on the method selected.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item border border-gray-200 rounded-md">
                        <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                            <span>Is the application fee refundable if I'm not accepted?</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden p-4 pt-0 text-gray-700">
                            <p>No, the application fee is non-refundable regardless of the admission decision. It covers the administrative costs of processing your application.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item border border-gray-200 rounded-md">
                        <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                            <span>Are there any additional fees I should be aware of?</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden p-4 pt-0 text-gray-700">
                            <p>Besides the application fee, there may be additional fees depending on your situation:</p>
                            <ul class="list-disc list-inside mt-2">
                                <li>Foreign credential evaluation fee (for international students)</li>
                                <li>Late application fee (if applying after the deadline)</li>
                                <li>Credit transfer evaluation fee (for transfer students)</li>
                            </ul>
                            <p class="mt-2">Upon acceptance, you will receive a comprehensive breakdown of tuition and other fees.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item border border-gray-200 rounded-md">
                        <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                            <span>Are scholarships or financial aid available?</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden p-4 pt-0 text-gray-700">
                            <p>Yes, we offer various scholarships and financial aid options for eligible students. After you've been accepted, you can apply for scholarships through our Financial Aid Office. Scholarship applications are evaluated separately from admission applications.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Other Questions FAQs -->
            <div class="faq-category" data-category="other">
                <h2 class="text-2xl font-bold text-dark-purple mb-6">Other Questions</h2>
                
                <div class="space-y-4">
                    <div class="faq-item border border-gray-200 rounded-md">
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
                    
                    <div class="faq-item border border-gray-200 rounded-md">
                        <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                            <span>Can I change my program choice after submitting my application?</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden p-4 pt-0 text-gray-700">
                            <p>Yes, you can request to change your program choice after submitting your application, but before a decision has been made. To do so, please email the Admissions Office at <strong>admissions@example.edu</strong> with your reference number and your new program preference.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item border border-gray-200 rounded-md">
                        <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                            <span>Is on-campus housing available for students?</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden p-4 pt-0 text-gray-700">
                            <p>Yes, on-campus housing is available for students on a first-come, first-served basis. Once you've been accepted, you can apply for housing through the Student Housing Office. We recommend applying early as housing options fill up quickly.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item border border-gray-200 rounded-md">
                        <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                            <span>What support services are available for students with disabilities?</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden p-4 pt-0 text-gray-700">
                            <p>We are committed to providing an inclusive environment for all students. Our Disability Support Services office provides accommodations and support for students with disabilities. If you require specific accommodations, please indicate this on your application or contact the Disability Support Services office at <strong>dss@example.edu</strong>.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item border border-gray-200 rounded-md">
                        <button class="faq-toggle flex justify-between items-center w-full p-4 text-left font-medium text-dark-purple focus:outline-none">
                            <span>What is the deferment policy if I'm accepted but can't attend immediately?</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden p-4 pt-0 text-gray-700">
                            <p>If you're accepted but unable to attend immediately, you may request to defer your admission for up to one academic year. Deferment requests are evaluated on a case-by-case basis. To request a deferment, please email the Admissions Office at <strong>admissions@example.edu</strong> with your reference number and reason for deferment.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- No Results Message -->
            <div id="no-faq-results" class="hidden text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-2xl font-bold text-gray-700 mt-4">No matching questions found</h3>
                <p class="text-gray-600 mt-2">Try different search terms or browse categories</p>
            </div>
        </div>
    </div>
</section>

<!-- Still Have Questions Section -->
<section class="bg-gradient-to-r from-dark-purple to-purple rounded-xl text-white p-8 text-center mb-16">
    <h2 class="text-3xl font-bold mb-4">Still Have Questions?</h2>
    <p class="text-xl mb-8 max-w-3xl mx-auto">
        If you couldn't find the answer to your question, feel free to contact our admissions team.
    </p>
    <div class="flex flex-wrap justify-center gap-4">
        <a href="contact.php" class="bg-white text-dark-purple hover:bg-gray-100 px-8 py-3 rounded-md font-medium transition duration-300 btn-hover">Contact Us</a>
        <a href="tel:+15551234567" class="border-2 border-white text-white hover:bg-white hover:text-dark-purple px-8 py-3 rounded-md font-medium transition duration-300 btn-hover">
            <span class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                Call Us
            </span>
        </a>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // FAQ toggles
        const faqToggles = document.querySelectorAll('.faq-toggle');
        const faqCategoryBtns = document.querySelectorAll('.faq-category-btn');
        const faqCategories = document.querySelectorAll('.faq-category');
        const faqItems = document.querySelectorAll('.faq-item');
        const faqSearchInput = document.getElementById('faq-search');
        const noFaqResults = document.getElementById('no-faq-results');
        
        // Toggle FAQ answers
        faqToggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                const content = toggle.nextElementSibling;
                const arrow = toggle.querySelector('svg');
                
                content.classList.toggle('hidden');
                arrow.classList.toggle('rotate-180');
            });
        });
        
        // Filter FAQs by category
        faqCategoryBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Update active button
                faqCategoryBtns.forEach(b => b.classList.remove('active', 'bg-slight-purple', 'text-white'));
                btn.classList.add('active', 'bg-slight-purple', 'text-white');
                
                const category = btn.getAttribute('data-category');
                
                // Show/hide categories
                if (category === 'all') {
                    faqCategories.forEach(cat => cat.classList.remove('hidden'));
                } else {
                    faqCategories.forEach(cat => {
                        if (cat.getAttribute('data-category') === category) {
                            cat.classList.remove('hidden');
                        } else {
                            cat.classList.add('hidden');
                        }
                    });
                }
                
                // Reset search
                faqSearchInput.value = '';
                faqItems.forEach(item => item.classList.remove('hidden'));
                noFaqResults.classList.add('hidden');
            });
        });
        
        // Search FAQs
        faqSearchInput.addEventListener('input', () => {
            const searchTerm = faqSearchInput.value.toLowerCase().trim();
            
            // Reset category filter
            const activeCategoryBtn = document.querySelector('.faq-category-btn.active');
            const activeCategory = activeCategoryBtn.getAttribute('data-category');
            
            let visibleItems = 0;
            
            faqItems.forEach(item => {
                const questionText = item.querySelector('.faq-toggle span').textContent.toLowerCase();
                const itemCategory = item.closest('.faq-category').getAttribute('data-category');
                
                // Check if item matches search and category filter
                const matchesSearch = searchTerm === '' || questionText.includes(searchTerm);
                const matchesCategory = activeCategory === 'all' || itemCategory === activeCategory;
                
                if (matchesSearch && matchesCategory) {
                    item.classList.remove('hidden');
                    visibleItems++;
                } else {
                    item.classList.add('hidden');
                }
            });
            
            // Show/hide no results message
            if (visibleItems === 0) {
                noFaqResults.classList.remove('hidden');
            } else {
                noFaqResults.classList.add('hidden');
            }
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>