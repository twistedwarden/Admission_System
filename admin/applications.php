<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

$pageTitle = 'Manage Applications';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Filters
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$type = isset($_GET['type']) ? sanitize($_GET['type']) : '';
$program = isset($_GET['program']) ? intval($_GET['program']) : 0;
$yearLevel = isset($_GET['year_level']) ? sanitize($_GET['year_level']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Build query
$query = "SELECT a.*, p.name as program_name 
          FROM applications a
          JOIN programs p ON a.program_id = p.id";

$countQuery = "SELECT COUNT(*) as total FROM applications a";

// Where clauses
$whereClauses = [];
$params = [];

if (!empty($status)) {
    $whereClauses[] = "a.status = ?";
    $params[] = $status;
}

if (!empty($type)) {
    $whereClauses[] = "a.applicant_type = ?";
    $params[] = $type;
}

if ($program > 0) {
    $whereClauses[] = "a.program_id = ?";
    $params[] = $program;
}

if (!empty($yearLevel)) {
    $whereClauses[] = "a.year_level = ?";
    $params[] = $yearLevel;
}

if (!empty($search)) {
    $whereClauses[] = "(a.reference_no LIKE ? OR a.first_name LIKE ? OR a.last_name LIKE ? OR a.email LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}

// Add where clauses to query
if (!empty($whereClauses)) {
    $query .= " WHERE " . implode(" AND ", $whereClauses);
    $countQuery .= " WHERE " . implode(" AND ", $whereClauses);
}

// Add order by and limit
$query .= " ORDER BY a.created_at DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;

// Get applications
$applications = fetchAll($query, $params);

// Get total count
$countParams = $params;
array_pop($countParams); // Remove offset
array_pop($countParams); // Remove limit
$totalCount = fetchOne($countQuery, $countParams);
$totalPages = ceil($totalCount['total'] / $perPage);

// Get all programs for filter
$programs = getPrograms();

require_once 'admin_header.php';
?>

<!-- Applications Content -->
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-dark-purple">Applications</h1>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form action="applications.php" method="get" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="status" class="block text-gray-700 font-medium mb-2">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="under_review" <?= $status === 'under_review' ? 'selected' : '' ?>>Under Review</option>
                    <option value="accepted" <?= $status === 'accepted' ? 'selected' : '' ?>>Accepted</option>
                    <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    <option value="incomplete" <?= $status === 'incomplete' ? 'selected' : '' ?>>Incomplete</option>
                </select>
            </div>
            
            <div>
                <label for="type" class="block text-gray-700 font-medium mb-2">Applicant Type</label>
                <select id="type" name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="new" <?= $type === 'new' ? 'selected' : '' ?>>New Student</option>
                    <option value="returning" <?= $type === 'returning' ? 'selected' : '' ?>>Returning Student</option>
                    <option value="transfer" <?= $type === 'transfer' ? 'selected' : '' ?>>Transfer Student</option>
                </select>
            </div>
            
            <div>
                <label for="program" class="block text-gray-700 font-medium mb-2">Program</label>
                <select id="program" name="program" class="form-select">
                    <option value="">All Programs</option>
                    <?php foreach ($programs as $prog): ?>
                        <option value="<?= $prog['id'] ?>" <?= $program === $prog['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($prog['code'] . ' - ' . $prog['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="year_level" class="block text-gray-700 font-medium mb-2">Year Level</label>
                <select id="year_level" name="year_level" class="form-select">
                    <option value="">All Year Levels</option>
                    <option value="1" <?= $yearLevel === '1' ? 'selected' : '' ?>>1st Year</option>
                    <option value="2" <?= $yearLevel === '2' ? 'selected' : '' ?>>2nd Year</option>
                    <option value="3" <?= $yearLevel === '3' ? 'selected' : '' ?>>3rd Year</option>
                    <option value="4" <?= $yearLevel === '4' ? 'selected' : '' ?>>4th Year</option>
                    <option value="5" <?= $yearLevel === '5' ? 'selected' : '' ?>>5th Year</option>
                </select>
            </div>
            
            <div>
                <label for="search" class="block text-gray-700 font-medium mb-2">Search</label>
                <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Ref no, name, email..." class="form-input">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="btn-primary">Filter</button>
                <a href="applications.php" class="btn-secondary">Reset</a>
            </div>
        </form>
    </div>
    
    <!-- Applications Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase text-sm leading-normal">
                        <th class="table-header">Ref No</th>
                        <th class="table-header">Name</th>
                        <th class="table-header">Program</th>
                        <th class="table-header">Year Level</th>
                        <th class="table-header">Type</th>
                        <th class="table-header">Status</th>
                        <th class="table-header">Payment</th>
                        <th class="table-header">Date</th>
                        <th class="table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm">
                    <?php if (count($applications) > 0): ?>
                        <?php foreach ($applications as $app): ?>
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
                            
                            $paymentColors = [
                                'unpaid' => 'red',
                                'processing' => 'yellow',
                                'paid' => 'green',
                            ];
                            $paymentColor = $paymentColors[$app['payment_status']] ?? 'gray';
                            $paymentText = ucfirst($app['payment_status']);
                            ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="table-cell">
                                    <?= $app['reference_no'] ?>
                                </td>
                                <td class="table-cell">
                                    <?= htmlspecialchars($app['first_name'] . ' ' . $app['last_name']) ?>
                                </td>
                                <td class="table-cell">
                                    <?= htmlspecialchars($app['program_name']) ?>
                                </td>
                                <td class="table-cell">
                                    <?= htmlspecialchars($app['year_level']) ?>
                                </td>
                                <td class="table-cell">
                                    <span class="capitalize"><?= $app['applicant_type'] ?></span>
                                </td>
                                <td class="table-cell">
                                    <span class="bg-<?= $statusColor ?>-100 text-<?= $statusColor ?>-800 rounded-full px-3 py-1 text-xs">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="table-cell">
                                    <span class="bg-<?= $paymentColor ?>-100 text-<?= $paymentColor ?>-800 rounded-full px-3 py-1 text-xs">
                                        <?= $paymentText ?>
                                    </span>
                                </td>
                                <td class="table-cell">
                                    <?= date('M d, Y', strtotime($app['created_at'])) ?>
                                </td>
                                <td class="table-cell">
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
                            <td colspan="8" class="py-4 px-6 text-center">No applications found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="flex justify-center mt-6">
            <div class="flex space-x-1">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&status=<?= $status ?>&type=<?= $type ?>&program=<?= $program ?>&year_level=<?= $yearLevel ?>&search=<?= urlencode($search) ?>" class="px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Previous
                    </a>
                <?php endif; ?>
                
                <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                ?>
                
                <?php if ($startPage > 1): ?>
                    <a href="?page=1&status=<?= $status ?>&type=<?= $type ?>&program=<?= $program ?>&year_level=<?= $yearLevel ?>&search=<?= urlencode($search) ?>" class="px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        1
                    </a>
                    <?php if ($startPage > 2): ?>
                        <span class="px-4 py-2">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="?page=<?= $i ?>&status=<?= $status ?>&type=<?= $type ?>&program=<?= $program ?>&year_level=<?= $yearLevel ?>&search=<?= urlencode($search) ?>" class="px-4 py-2 <?= $i === $page ? 'bg-purple text-white' : 'bg-white border border-gray-300 hover:bg-gray-50' ?> rounded-md">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <span class="px-4 py-2">...</span>
                    <?php endif; ?>
                    <a href="?page=<?= $totalPages ?>&status=<?= $status ?>&type=<?= $type ?>&program=<?= $program ?>&year_level=<?= $yearLevel ?>&search=<?= urlencode($search) ?>" class="px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        <?= $totalPages ?>
                    </a>
                <?php endif; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&status=<?= $status ?>&type=<?= $type ?>&program=<?= $program ?>&year_level=<?= $yearLevel ?>&search=<?= urlencode($search) ?>" class="px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Next
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'admin_footer.php'; ?>
