<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

$pageTitle = 'Admin Dashboard';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Get admin information
$admin = getAdminById($_SESSION['admin_id']);

// Get application statistics
$stats = countApplicationsByStatus();

// Get admission statistics by program
$admissionsByProgram = fetchAll(
    "SELECT 
        p.name as program_name,
        COUNT(*) as total,
        SUM(CASE WHEN a.status = 'accepted' THEN 1 ELSE 0 END) as accepted
     FROM applications a
     JOIN programs p ON a.program_id = p.id
     GROUP BY p.name
     ORDER BY total DESC"
);

// Get admission statistics by year
$admissionsByYear = fetchAll(
    "SELECT 
        YEAR(created_at) as year,
        COUNT(*) as total,
        SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted
     FROM applications
     GROUP BY YEAR(created_at)
     ORDER BY year DESC"
);



// Get admission statistics by day
$admissionsByDay = fetchAll(
    "SELECT 
        DATE(created_at) as date,
        COUNT(*) as total,
        SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted
     FROM applications
     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
     GROUP BY DATE(created_at)
     ORDER BY date DESC"
);

// Get admission statistics by week
$admissionsByWeek = fetchAll(
    "SELECT 
        YEARWEEK(created_at) as week,
        COUNT(*) as total,
        SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted
     FROM applications
     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 WEEK)
     GROUP BY YEARWEEK(created_at)
     ORDER BY week DESC"
);

// Get recent applications
$recentApplications = fetchAll(
    "SELECT a.*, p.name as program_name 
     FROM applications a
     JOIN programs p ON a.program_id = p.id
     ORDER BY a.created_at DESC
     LIMIT 10"
);

// Get applications by type
$applicationsByType = fetchOne(
    "SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN applicant_type = 'new' THEN 1 ELSE 0 END) AS new_students,
        SUM(CASE WHEN applicant_type = 'returning' THEN 1 ELSE 0 END) AS returning_students,
        SUM(CASE WHEN applicant_type = 'transfer' THEN 1 ELSE 0 END) AS transfer_students
     FROM applications"
);

require_once 'admin_header.php';
?>

<!-- Dashboard Content -->
<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-dark-purple">Dashboard</h1>
        <div class="text-gray-600">
            <span>Today's Date: </span>
            <span class="font-medium"><?= date('l, F j, Y') ?></span>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-purple">
            <h3 class="text-gray-500 font-medium mb-2">Total Applications</h3>
            <p class="text-3xl font-bold text-dark-purple"><?= $stats['total'] ?? 0 ?></p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-yellow-400">
            <h3 class="text-gray-500 font-medium mb-2">Pending</h3>
            <p class="text-3xl font-bold text-yellow-600"><?= $stats['pending'] ?? 0 ?></p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-blue-400">
            <h3 class="text-gray-500 font-medium mb-2">Under Review</h3>
            <p class="text-3xl font-bold text-blue-600"><?= $stats['under_review'] ?? 0 ?></p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-400">
            <h3 class="text-gray-500 font-medium mb-2">Accepted</h3>
            <p class="text-3xl font-bold text-green-600"><?= $stats['accepted'] ?? 0 ?></p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-red-400">
            <h3 class="text-gray-500 font-medium mb-2">Rejected</h3>
            <p class="text-3xl font-bold text-red-600"><?= $stats['rejected'] ?? 0 ?></p>
        </div>
    </div>
    
    <!-- Admission Statistics -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-dark-purple mb-6">Admission Statistics</h2>
        
        <!-- Tabs -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button class="tab-button active" data-tab="program">By Program</button>
                <button class="tab-button" data-tab="year">By Year</button>
                <button class="tab-button" data-tab="week">By Week</button>
                <button class="tab-button" data-tab="day">By Day</button>
            </nav>
        </div>
        
        <!-- Year Statistics -->
        <div class="tab-content hidden" id="year-tab">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Year</th>
                            <th class="py-3 px-6 text-left">Total Applications</th>
                            <th class="py-3 px-6 text-left">Accepted</th>
                            <th class="py-3 px-6 text-left">Acceptance Rate</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm">
                        <?php foreach ($admissionsByYear as $year): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-6"><?= $year['year'] ?></td>
                                <td class="py-3 px-6"><?= $year['total'] ?></td>
                                <td class="py-3 px-6"><?= $year['accepted'] ?></td>
                                <td class="py-3 px-6"><?= round(($year['accepted'] / $year['total']) * 100, 2) ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Program Statistics -->
        <div class="tab-content active" id="program-tab">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Program</th>
                            <th class="py-3 px-6 text-left">Total Applications</th>
                            <th class="py-3 px-6 text-left">Accepted</th>
                            <th class="py-3 px-6 text-left">Acceptance Rate</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm">
                        <?php foreach ($admissionsByProgram as $program): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-6"><?= htmlspecialchars($program['program_name']) ?></td>
                                <td class="py-3 px-6"><?= $program['total'] ?></td>
                                <td class="py-3 px-6"><?= $program['accepted'] ?></td>
                                <td class="py-3 px-6"><?= round(($program['accepted'] / $program['total']) * 100, 2) ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Daily Statistics -->
        <div class="tab-content hidden" id="day-tab">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Date</th>
                            <th class="py-3 px-6 text-left">Total Applications</th>
                            <th class="py-3 px-6 text-left">Accepted</th>
                            <th class="py-3 px-6 text-left">Acceptance Rate</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm">
                        <?php foreach ($admissionsByDay as $day): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-6"><?= date('M d, Y', strtotime($day['date'])) ?></td>
                                <td class="py-3 px-6"><?= $day['total'] ?></td>
                                <td class="py-3 px-6"><?= $day['accepted'] ?></td>
                                <td class="py-3 px-6"><?= round(($day['accepted'] / $day['total']) * 100, 2) ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Weekly Statistics -->
        <div class="tab-content hidden" id="week-tab">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Week</th>
                            <th class="py-3 px-6 text-left">Total Applications</th>
                            <th class="py-3 px-6 text-left">Accepted</th>
                            <th class="py-3 px-6 text-left">Acceptance Rate</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm">
                        <?php foreach ($admissionsByWeek as $week): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-6">Week <?= substr($week['week'], 4) ?>, <?= substr($week['week'], 0, 4) ?></td>
                                <td class="py-3 px-6"><?= $week['total'] ?></td>
                                <td class="py-3 px-6"><?= $week['accepted'] ?></td>
                                <td class="py-3 px-6"><?= round(($week['accepted'] / $week['total']) * 100, 2) ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Charts and Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Application Status Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-dark-purple mb-4">Application Status</h2>
            <div class="relative" style="height: 300px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
        
        <!-- Applicant Types Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-dark-purple mb-4">Applicant Types</h2>
            <div class="relative" style="height: 300px;">
                <canvas id="typeChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Recent Applications -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-dark-purple">Recent Applications</h2>
            <a href="applications.php" class="text-purple hover:text-dark-purple transition duration-300">View All &rarr;</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Ref No</th>
                        <th class="py-3 px-6 text-left">Name</th>
                        <th class="py-3 px-6 text-left">Program</th>
                        <th class="py-3 px-6 text-left">Year Level</th>
                        <th class="py-3 px-6 text-left">Type</th>
                        <th class="py-3 px-6 text-left">Status</th>
                        <th class="py-3 px-6 text-left">Date</th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm">
                    <?php if (count($recentApplications) > 0): ?>
                        <?php foreach ($recentApplications as $app): ?>
                            <?php
                            $statusColors = [
                                'pending' => 'yellow',
                                'under_review' => 'blue',
                                'accepted' => 'green',
                                'rejected' => 'red',
                                'incomplete' => 'gray',
                            ];
                            $statusColor = $statusColors[$app['status']] ?? 'gray';
                            $statusText = ucwords(str_replace('_', ' ', $app['status']));
                            ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-6 text-left">
                                    <?= $app['reference_no'] ?>
                                </td>
                                <td class="py-3 px-6 text-left">
                                    <?= htmlspecialchars($app['first_name'] . ' ' . $app['last_name']) ?>
                                </td>
                                <td class="py-3 px-6 text-left">
                                    <?= htmlspecialchars($app['program_name']) ?>
                                </td>
                                <td class="py-3 px-6 text-left">
                                    <?= htmlspecialchars($app['year_level']) ?>
                                </td>
                                <td class="py-3 px-6 text-left">
                                    <span class="capitalize"><?= $app['applicant_type'] ?></span>
                                </td>
                                <td class="py-3 px-6 text-left">
                                    <span class="bg-<?= $statusColor ?>-100 text-<?= $statusColor ?>-800 rounded-full px-3 py-1 text-xs">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-left">
                                    <?= date('M d, Y', strtotime($app['created_at'])) ?>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex item-center justify-center">
                                        <a href="view_application.php?id=<?= $app['id'] ?>" class="w-4 mr-2 transform hover:text-purple hover:scale-110">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="edit_application.php?id=<?= $app['id'] ?>" class="w-4 mr-2 transform hover:text-purple hover:scale-110">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="border-b border-gray-200">
                            <td colspan="7" class="py-4 px-6 text-center">No applications found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-dark-purple mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="applications.php?status=pending" class="block p-4 bg-yellow-50 rounded-lg border border-yellow-200 hover:bg-yellow-100 transition duration-300">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">View Pending Applications</span>
                </div>
            </a>
            
            <a href="applications.php?status=under_review" class="block p-4 bg-blue-50 rounded-lg border border-blue-200 hover:bg-blue-100 transition duration-300">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-medium">Review Applications</span>
                </div>
            </a>
            
            <a href="programs.php" class="block p-4 bg-purple-50 rounded-lg border border-purple-200 hover:bg-purple-100 transition duration-300">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="font-medium">Manage Programs</span>
                </div>
            </a>
            
            <a href="reports.php" class="block p-4 bg-green-50 rounded-lg border border-green-200 hover:bg-green-100 transition duration-300">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-medium">Generate Reports</span>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Charts.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.add('hidden'));
                
                // Add active class to clicked button and corresponding content
                button.classList.add('active');
                const tabId = button.getAttribute('data-tab');
                document.getElementById(`${tabId}-tab`).classList.remove('hidden');
            });
        });
        
        // Application Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Under Review', 'Accepted', 'Rejected', 'Incomplete'],
                datasets: [{
                    data: [
                        <?= $stats['pending'] ?? 0 ?>, 
                        <?= $stats['under_review'] ?? 0 ?>, 
                        <?= $stats['accepted'] ?? 0 ?>, 
                        <?= $stats['rejected'] ?? 0 ?>, 
                        <?= $stats['incomplete'] ?? 0 ?>
                    ],
                    backgroundColor: [
                        '#FCD34D', // yellow
                        '#60A5FA', // blue
                        '#34D399', // green
                        '#F87171', // red
                        '#9CA3AF'  // gray
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
        
        // Applicant Types Chart
        const typeCtx = document.getElementById('typeChart').getContext('2d');
        const typeChart = new Chart(typeCtx, {
            type: 'bar',
            data: {
                labels: ['New', 'Returning', 'Transfer'],
                datasets: [{
                    label: 'Number of Applicants',
                    data: [
                        <?= $applicationsByType['new_students'] ?? 0 ?>, 
                        <?= $applicationsByType['returning_students'] ?? 0 ?>, 
                        <?= $applicationsByType['transfer_students'] ?? 0 ?>
                    ],
                    backgroundColor: [
                        '#8243D9', // purple
                        '#5B26A6', // medium-dark-purple
                        '#3B1273'  // dark-purple
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>

<style>
    .tab-button {
        padding: 1rem 0.25rem;
        border-bottom-width: 2px;
        font-weight: 500;
        font-size: 0.875rem;
        border-color: transparent;
    }
    
    .tab-button.active {
        border-color: #8243D9;
        color: #8243D9;
    }
    
    .tab-button:not(.active) {
        color: #6B7280;
    }
    
    .tab-button:not(.active):hover {
        color: #374151;
        border-color: #D1D5DB;
    }
</style>

<?php require_once 'admin_footer.php'; ?>
