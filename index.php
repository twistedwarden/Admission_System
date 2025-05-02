<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pageTitle = 'Home';

// Get programs for display
$programs = getPrograms();



require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="py-12 md:py-20">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div class="animate-on-scroll">
            <h1 class="text-4xl md:text-5xl font-bold text-dark-purple mb-6">
                Begin Your Academic Journey With Us
            </h1>
            <p class="text-lg text-gray-700 mb-8">
                We provide a simple and efficient admission process for new, returning, and transfer students. 
                Join our diverse community of learners and achieve your academic goals.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="apply/new.php" class="btn-primary">Apply Now</a>
                <a href="programs.php" class="btn-outline">Explore Programs</a>
            </div>
        </div>
        <div class="animate-on-scroll">
            <img src="https://images.pexels.com/photos/267885/pexels-photo-267885.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" 
                 alt="Students at university" 
                 class="rounded-lg shadow-xl w-full h-auto object-cover">
        </div>
    </div>
</section>

<!-- Student Types Section -->
<section class="py-12 md:py-16 px-4 bg-[#D3B3F2] rounded-xl my-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-dark-purple mb-4">Choose Your Path</h2>
        <p class="text-lg text-gray-700 max-w-3xl mx-auto">
            Whether you're starting fresh, continuing your education, or transferring from another institution,
            we have a streamlined process for you.
        </p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- New Student -->
        <div class="bg-white rounded-lg shadow-md p-8 transition duration-300 card-hover animate-on-scroll">
            <div class="w-16 h-16 rounded-full bg-purple flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-dark-purple text-center mb-4">New Student</h3>
            <p class="text-gray-700 mb-6 text-center">
                Starting your academic journey with us? Apply as a new student and embark on an exciting educational experience.
            </p>
            <div class="text-center">
                <a href="apply/new.php" class="btn-primary inline-block">Apply as New Student</a>
            </div>
        </div>
        
        <!-- Returning Student -->
        <div class="bg-white rounded-lg shadow-md p-8 transition duration-300 card-hover animate-on-scroll">
            <div class="w-16 h-16 rounded-full bg-purple flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-dark-purple text-center mb-4">Returning Student</h3>
            <p class="text-gray-700 mb-6 text-center">
                Coming back to continue your education? Welcome back! Complete your returning student application.
            </p>
            <div class="text-center">
                <a href="apply/returning.php" class="btn-primary inline-block">Apply as Returning Student</a>
            </div>
        </div>
        
        <!-- Transfer Student -->
        <div class="bg-white rounded-lg shadow-md p-8 transition duration-300 card-hover animate-on-scroll">
            <div class="w-16 h-16 rounded-full bg-purple flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-dark-purple text-center mb-4">Transfer Student</h3>
            <p class="text-gray-700 mb-6 text-center">
                Transferring from another institution? We make the transition smooth with our transfer student application.
            </p>
            <div class="text-center">
                <a href="apply/transfer.php" class="btn-primary inline-block">Apply as Transfer Student</a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Programs -->
<section class="py-12 md:py-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-dark-purple mb-4">Featured Programs</h2>
        <p class="text-lg text-gray-700 max-w-3xl mx-auto">
            Discover our diverse range of academic programs designed to prepare you for success in your chosen field.
        </p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach (array_slice($programs, 0, 6) as $program): ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 card-hover animate-on-scroll">
            <div class="h-48 bg-gradient-to-r from-purple to-dark-purple flex items-center justify-center">
                <h3 class="text-2xl font-bold text-white"><?= htmlspecialchars($program['code']) ?></h3>
            </div>
            <div class="p-6">
                <h4 class="text-xl font-bold text-dark-purple mb-4"><?= htmlspecialchars($program['name']) ?></h4>
                <p class="text-gray-700 mb-6">
                    <?= nl2br(htmlspecialchars($program['description'])) ?>
                </p>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-medium-dark-purple font-medium"><?= htmlspecialchars($program['department']) ?></span>
                    <a href="apply/new.php?program=<?= $program['id'] ?>" class="text-purple hover:text-dark-purple font-medium transition duration-300">Apply Now &rarr;</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-12">
        <a href="programs.php" class="btn-secondary">View All Programs</a>
    </div>
</section>

<!-- Features Section -->
<section class="py-12 md:py-16 px-4 bg-[#D3B3F2] rounded-xl my-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-dark-purple mb-4">Why Choose Us</h2>
        <p class="text-lg text-gray-700 max-w-3xl mx-auto">
            Our admission process is designed with your convenience in mind, offering various features to make your application journey smooth.
        </p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Online Application -->
        <div class="bg-white rounded-lg shadow-md p-6 transition duration-300 card-hover animate-on-scroll">
            <div class="w-12 h-12 rounded-full bg-slight-purple flex items-center justify-center mb-6">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-dark-purple mb-3">Online Application</h3>
            <p class="text-gray-700">
                Complete your application conveniently online from anywhere, anytime.
            </p>
        </div>
        
        <!-- Document Upload -->
        <div class="bg-white rounded-lg shadow-md p-6 transition duration-300 card-hover animate-on-scroll">
            <div class="w-12 h-12 rounded-full bg-slight-purple flex items-center justify-center mb-6">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-dark-purple mb-3">Document Upload</h3>
            <p class="text-gray-700">
                Easily upload required documents directly through our secure portal.
            </p>
        </div>
        
        <!-- Online Payment -->
        <div class="bg-white rounded-lg shadow-md p-6 transition duration-300 card-hover animate-on-scroll">
            <div class="w-12 h-12 rounded-full bg-slight-purple flex items-center justify-center mb-6">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-dark-purple mb-3">Online Payment</h3>
            <p class="text-gray-700">
                Pay admission fees securely online with multiple payment options.
            </p>
        </div>
        
        <!-- Status Tracking -->
        <div class="bg-white rounded-lg shadow-md p-6 transition duration-300 card-hover animate-on-scroll">
            <div class="w-12 h-12 rounded-full bg-slight-purple flex items-center justify-center mb-6">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-dark-purple mb-3">Status Tracking</h3>
            <p class="text-gray-700">
                Track your application status in real-time and receive email updates.
            </p>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-12 md:py-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-dark-purple mb-4">Student Testimonials</h2>
        <p class="text-lg text-gray-700 max-w-3xl mx-auto">
            Hear what our students have to say about their experience with our admission process.
        </p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Testimonial 1 -->
        <div class="bg-white rounded-lg shadow-md p-8 transition duration-300 card-hover animate-on-scroll">
            <div class="flex justify-center mb-6">
                <div class="relative">
                    <img src="https://images.pexels.com/photos/837358/pexels-photo-837358.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Student" class="w-24 h-24 rounded-full object-cover">
                    <div class="absolute -bottom-2 -right-2 bg-purple rounded-full p-1">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <p class="text-gray-700 mb-6 italic text-center">
                "The admission process was incredibly smooth. I was able to upload all my documents easily and track my application status in real-time."
            </p>
            <div class="text-center">
                <h4 class="font-bold text-dark-purple">Sarah Johnson</h4>
                <p class="text-sm text-gray-600">Computer Science Student</p>
            </div>
        </div>
        
        <!-- Testimonial 2 -->
        <div class="bg-white rounded-lg shadow-md p-8 transition duration-300 card-hover animate-on-scroll">
            <div class="flex justify-center mb-6">
                <div class="relative">
                    <img src="https://images.pexels.com/photos/846741/pexels-photo-846741.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Student" class="w-24 h-24 rounded-full object-cover">
                    <div class="absolute -bottom-2 -right-2 bg-purple rounded-full p-1">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <p class="text-gray-700 mb-6 italic text-center">
                "As a transfer student, I was worried about the process, but the staff was incredibly helpful and the online system made everything simple."
            </p>
            <div class="text-center">
                <h4 class="font-bold text-dark-purple">Michael Chen</h4>
                <p class="text-sm text-gray-600">Engineering Student</p>
            </div>
        </div>
        
        <!-- Testimonial 3 -->
        <div class="bg-white rounded-lg shadow-md p-8 transition duration-300 card-hover animate-on-scroll">
            <div class="flex justify-center mb-6">
                <div class="relative">
                    <img src="https://images.pexels.com/photos/3762800/pexels-photo-3762800.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Student" class="w-24 h-24 rounded-full object-cover">
                    <div class="absolute -bottom-2 -right-2 bg-purple rounded-full p-1">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <p class="text-gray-700 mb-6 italic text-center">
                "I appreciated the email updates about my application status. The payment process was secure and the entire experience exceeded my expectations."
            </p>
            <div class="text-center">
                <h4 class="font-bold text-dark-purple">Emily Rodriguez</h4>
                <p class="text-sm text-gray-600">Business Administration Student</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 md:py-20 bg-gradient-to-r from-dark-purple to-purple rounded-xl text-white text-center">
    <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Begin Your Journey?</h2>
    <p class="text-xl mb-8 max-w-3xl mx-auto">
        Take the first step towards your academic goals. Apply today and join our community of learners.
    </p>
    <div class="flex flex-wrap justify-center gap-4">
        <a href="apply/new.php" class="bg-white text-dark-purple hover:bg-gray-100 px-8 py-3 rounded-md font-medium transition duration-300 btn-hover">Apply Now</a>
        <a href="contact.php" class="border-2 border-white text-white hover:bg-white hover:text-dark-purple px-8 py-3 rounded-md font-medium transition duration-300 btn-hover">Contact Us</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>