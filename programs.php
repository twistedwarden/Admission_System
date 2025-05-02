<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pageTitle = 'Programs';

// Get all programs
$programs = getPrograms();

// Group programs by department for easier display
$programsByDepartment = [];
foreach ($programs as $program) {
    $programsByDepartment[$program['department']][] = $program;
}

require_once 'includes/header.php';
?>

<!-- Programs Header -->
<section class="py-12 header-gradient text-white rounded-xl mb-12">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-6">Our Academic Programs</h1>
        <p class="text-xl max-w-3xl mx-auto">
            Discover our diverse range of academic programs designed to help you achieve your career goals
            and make a positive impact on society.
        </p>
    </div>
</section>

<!-- Search and Filter -->
<section class="mb-12">
    <div class="bg-[#D3B3F2] p-6 rounded-lg shadow-md">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-grow">
                <input type="text" id="program-search" placeholder="Search programs..." class="form-input">
            </div>
            <div class="md:w-1/3">
                <select id="department-filter" class="form-select">
                    <option value="">All Departments</option>
                    <?php foreach (array_keys($programsByDepartment) as $department): ?>
                        <option value="<?= htmlspecialchars($department) ?>"><?= htmlspecialchars($department) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- Programs List -->
<section id="programs-container">
    <?php foreach ($programsByDepartment as $department => $departmentPrograms): ?>
    <div class="department-section mb-16" data-department="<?= htmlspecialchars($department) ?>">
        <h2 class="text-2xl font-bold text-dark-purple mb-6 pb-2 border-b-2 border-slight-purple">
            <?= htmlspecialchars($department) ?>
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($departmentPrograms as $program): ?>
            <div class="program-card bg-white rounded-lg shadow-md overflow-hidden transition duration-300 card-hover animate-on-scroll" 
                 data-program="<?= htmlspecialchars(strtolower($program['name'])) ?>"
                 data-code="<?= htmlspecialchars(strtolower($program['code'])) ?>">
                <div class="h-32 gradient-bg flex items-center justify-center">
                    <h3 class="text-2xl font-bold text-white">
                        <?= htmlspecialchars($program['code']) ?>
                    </h3>
                </div>
                <div class="p-6">
                    <h4 class="text-xl font-bold text-dark-purple mb-4">
                        <?= htmlspecialchars($program['name']) ?>
                    </h4>
                    <p class="text-gray-700 mb-6">
                        <?= nl2br(htmlspecialchars($program['description'])) ?>
                    </p>
                    <div class="flex justify-end">
                        <a href="apply/new.php?program=<?= $program['id'] ?>" 
                           class="btn-primary">Apply Now</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
    
    <div id="no-results" class="hidden text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="text-2xl font-bold text-gray-700 mt-4">No programs found</h3>
        <p class="text-gray-600 mt-2">Try different search terms or filters</p>
    </div>
</section>

<!-- Apply Now CTA -->
<section class="py-16 md:py-20 bg-gradient-to-r from-dark-purple to-purple rounded-xl text-white text-center">
    <h2 class="text-3xl md:text-4xl font-bold mb-6">Found a Program That Interests You?</h2>
    <p class="text-xl mb-8 max-w-3xl mx-auto">
        Start your application today and take the first step toward your future career.
    </p>
    <div class="flex flex-wrap justify-center gap-4">
        <a href="apply/new.php" class="bg-white text-dark-purple hover:bg-gray-100 px-8 py-3 rounded-md font-medium transition duration-300 btn-hover">Apply Now</a>
        <a href="contact.php" class="border-2 border-white text-white hover:bg-white hover:text-dark-purple px-8 py-3 rounded-md font-medium transition duration-300 btn-hover">Contact Us</a>
    </div>
</section>

<!-- Program Search Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('program-search');
        const departmentFilter = document.getElementById('department-filter');
        const programCards = document.querySelectorAll('.program-card');
        const departmentSections = document.querySelectorAll('.department-section');
        const noResults = document.getElementById('no-results');
        
        function filterPrograms() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedDepartment = departmentFilter.value;
            
            let visiblePrograms = 0;
            const visibleDepartments = new Set();
            
            // Hide all department sections initially
            departmentSections.forEach(section => {
                section.classList.add('hidden');
            });
            
            // Filter program cards
            programCards.forEach(card => {
                const programName = card.getAttribute('data-program');
                const programCode = card.getAttribute('data-code');
                const departmentSection = card.closest('.department-section');
                const cardDepartment = departmentSection.getAttribute('data-department');
                
                // Check if card matches search and filter
                const matchesSearch = programName.includes(searchTerm) || programCode.includes(searchTerm);
                const matchesDepartment = !selectedDepartment || cardDepartment === selectedDepartment;
                
                if (matchesSearch && matchesDepartment) {
                    card.classList.remove('hidden');
                    departmentSection.classList.remove('hidden');
                    visiblePrograms++;
                    visibleDepartments.add(cardDepartment);
                } else {
                    card.classList.add('hidden');
                }
            });
            
            // Check for no results
            if (visiblePrograms === 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
            
            // Hide empty department sections
            departmentSections.forEach(section => {
                const department = section.getAttribute('data-department');
                const hasVisibleCards = section.querySelectorAll('.program-card:not(.hidden)').length > 0;
                
                if (!hasVisibleCards) {
                    section.classList.add('hidden');
                }
            });
        }
        
        // Add event listeners
        searchInput.addEventListener('input', filterPrograms);
        departmentFilter.addEventListener('change', filterPrograms);
    });
</script>

<?php require_once 'includes/footer.php'; ?>