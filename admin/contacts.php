<?php
// admin/contacts.php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
require_once '../include/functions.php';

// Mark as read
if(isset($_GET['read'])) {
    $stmt = $pdo->prepare("UPDATE contacts SET status = 'read' WHERE id = ?");
    $stmt->execute([$_GET['read']]);
    $_SESSION['success_message'] = 'Message marked as read.';
    header('Location: contacts.php');
    exit;
}

// Mark as replied
if(isset($_GET['reply'])) {
    $stmt = $pdo->prepare("UPDATE contacts SET status = 'replied' WHERE id = ?");
    $stmt->execute([$_GET['reply']]);
    $_SESSION['success_message'] = 'Message marked as replied.';
    header('Location: contacts.php');
    exit;
}

// Mark as unread
if(isset($_GET['unread'])) {
    $stmt = $pdo->prepare("UPDATE contacts SET status = 'unread' WHERE id = ?");
    $stmt->execute([$_GET['unread']]);
    $_SESSION['success_message'] = 'Message marked as unread.';
    header('Location: contacts.php');
    exit;
}

// Delete
if(isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $_SESSION['success_message'] = 'Message deleted successfully.';
    header('Location: contacts.php');
    exit;
}

// Get filter and search parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query
$query = "SELECT * FROM contacts WHERE 1=1";
$params = [];

if($status_filter != 'all') {
    $query .= " AND status = ?";
    $params[] = $status_filter;
}

if(!empty($search_term)) {
    $query .= " AND (name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $search_param = "%$search_term%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$query .= " ORDER BY submitted_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$contacts = $stmt->fetchAll();

// Get counts for each status
$total_count = $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
$unread_count = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status = 'unread'")->fetchColumn();
$read_count = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status = 'read'")->fetchColumn();
$replied_count = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status = 'replied'")->fetchColumn();

// Check for success message
$success = '';
if(isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="brand-font" style="color: var(--primary-blue);">
        <i class="bi bi-envelope me-2"></i> Contact Messages
    </h2>
    <span class="badge bg-primary"><?php echo $total_count; ?> Total</span>
</div>

<?php if($success): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i> <?php echo $success; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Filter and Search Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold"><i class="bi bi-search me-1"></i> Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name, email, subject..." value="<?php echo htmlspecialchars($search_term); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold"><i class="bi bi-filter me-1"></i> Filter by Status</label>
                <select name="status" class="form-select">
                    <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Messages (<?php echo $total_count; ?>)</option>
                    <option value="unread" <?php echo $status_filter == 'unread' ? 'selected' : ''; ?>>Unread (<?php echo $unread_count; ?>)</option>
                    <option value="read" <?php echo $status_filter == 'read' ? 'selected' : ''; ?>>Read (<?php echo $read_count; ?>)</option>
                    <option value="replied" <?php echo $status_filter == 'replied' ? 'selected' : ''; ?>>Replied (<?php echo $replied_count; ?>)</option>
                </select>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary-green w-50">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="contacts.php" class="btn btn-outline-secondary w-50">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Status Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card" style="border-left-color: #dc3545;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number" style="color: #dc3545;"><?php echo $unread_count; ?></div>
                    <div class="stat-label">Unread</div>
                </div>
                <div class="stat-icon" style="color: #dc3545; opacity: 0.5;"><i class="bi bi-envelope-exclamation"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card" style="border-left-color: #ffc107;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number" style="color: #ffc107;"><?php echo $read_count; ?></div>
                    <div class="stat-label">Read</div>
                </div>
                <div class="stat-icon" style="color: #ffc107; opacity: 0.5;"><i class="bi bi-envelope-open"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card" style="border-left-color: #28a745;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number" style="color: #28a745;"><?php echo $replied_count; ?></div>
                    <div class="stat-label">Replied</div>
                </div>
                <div class="stat-icon" style="color: #28a745; opacity: 0.5;"><i class="bi bi-reply-all"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card" style="border-left-color: var(--primary-blue);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number" style="color: var(--primary-blue);"><?php echo $total_count; ?></div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-icon" style="color: var(--primary-blue); opacity: 0.5;"><i class="bi bi-envelope"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Messages Table -->
<div class="card">
    <div class="card-header" style="background: linear-gradient(135deg, var(--primary-blue), var(--primary-green)); color: white;">
        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i> Messages</h5>
    </div>
    <div class="card-body">
        <?php if(empty($contacts)): ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
            <h5>No messages found</h5>
            <p class="text-muted"><?php echo !empty($search_term) || $status_filter != 'all' ? 'Try adjusting your search or filter criteria.' : 'No messages have been submitted yet.'; ?></p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($contacts as $contact): ?>
                    <tr class="<?php echo $contact['status'] == 'unread' ? 'table-warning' : ''; ?>">
                        <td>
                            <span class="badge <?php 
                                echo $contact['status'] == 'unread' ? 'bg-danger' : 
                                    ($contact['status'] == 'read' ? 'bg-warning text-dark' : 'bg-success'); 
                            ?>">
                                <?php if($contact['status'] == 'unread'): ?>
                                <i class="bi bi-envelope-exclamation me-1"></i>
                                <?php elseif($contact['status'] == 'read'): ?>
                                <i class="bi bi-envelope-open me-1"></i>
                                <?php else: ?>
                                <i class="bi bi-reply-all me-1"></i>
                                <?php endif; ?>
                                <?php echo ucfirst($contact['status']); ?>
                            </span>
                        </td>
                        <td><strong><?php echo htmlspecialchars($contact['name']); ?></strong></td>
                        <td><a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>"><?php echo htmlspecialchars($contact['email']); ?></a></td>
                        <td><?php echo htmlspecialchars($contact['subject']); ?></td>
                        <td>
                            <small><?php echo date('M j, Y', strtotime($contact['submitted_at'])); ?></small>
                            <br><small class="text-muted"><?php echo date('g:i A', strtotime($contact['submitted_at'])); ?></small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#messageModal<?php echo $contact['id']; ?>" title="View">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <?php if($contact['status'] == 'unread'): ?>
                                <a href="?read=<?php echo $contact['id']; ?>" class="btn btn-warning" title="Mark as Read">
                                    <i class="bi bi-check-circle"></i>
                                </a>
                                <?php endif; ?>
                                <?php if($contact['status'] == 'read'): ?>
                                <a href="?unread=<?php echo $contact['id']; ?>" class="btn btn-secondary" title="Mark as Unread">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </a>
                                <?php endif; ?>
                                <a href="?reply=<?php echo $contact['id']; ?>" class="btn btn-success" title="Mark as Replied">
                                    <i class="bi bi-reply"></i>
                                </a>
                                <a href="?delete=<?php echo $contact['id']; ?>" class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this message?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Table footer with count -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">Showing <?php echo count($contacts); ?> message(s)</small>
            <?php if(!empty($search_term) || $status_filter != 'all'): ?>
            <a href="contacts.php" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-counterclockwise"></i> Clear Filters
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modals for each message -->
<?php foreach($contacts as $contact): ?>
<div class="modal fade" id="messageModal<?php echo $contact['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--primary-blue), var(--primary-green)); color: white;">
                <h5 class="modal-title">
                    <i class="bi bi-person me-2"></i> Message from <?php echo htmlspecialchars($contact['name']); ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong><i class="bi bi-envelope me-1"></i> Email:</strong>
                        <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>"><?php echo htmlspecialchars($contact['email']); ?></a>
                    </div>
                    <div class="col-md-6">
                        <strong><i class="bi bi-phone me-1"></i> Phone:</strong>
                        <?php echo htmlspecialchars($contact['phone'] ?? 'N/A'); ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong><i class="bi bi-tag me-1"></i> Subject:</strong>
                        <?php echo htmlspecialchars($contact['subject']); ?>
                    </div>
                    <div class="col-md-6">
                        <strong><i class="bi bi-clock me-1"></i> Submitted:</strong>
                        <?php echo date('F j, Y g:i A', strtotime($contact['submitted_at'])); ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong><i class="bi bi-info-circle me-1"></i> Status:</strong>
                        <span class="badge <?php 
                            echo $contact['status'] == 'unread' ? 'bg-danger' : 
                                ($contact['status'] == 'read' ? 'bg-warning text-dark' : 'bg-success'); 
                        ?>">
                            <?php echo ucfirst($contact['status']); ?>
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong><i class="bi bi-hash me-1"></i> Message ID:</strong>
                        #<?php echo $contact['id']; ?>
                    </div>
                </div>
                <hr>
                <div class="message-content">
                    <strong><i class="bi bi-chat-dots me-1"></i> Message:</strong>
                    <div class="p-3 mt-2" style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid var(--primary-green);">
                        <?php echo nl2br(htmlspecialchars($contact['message'])); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="d-flex gap-2 w-100 justify-content-between">
                    <div>
                        <?php if($contact['status'] == 'unread'): ?>
                        <a href="?read=<?php echo $contact['id']; ?>" class="btn btn-warning">
                            <i class="bi bi-check-circle"></i> Mark as Read
                        </a>
                        <?php endif; ?>
                        <a href="?reply=<?php echo $contact['id']; ?>" class="btn btn-success">
                            <i class="bi bi-reply"></i> Mark as Replied
                        </a>
                    </div>
                    <div>
                        <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>" class="btn btn-primary">
                            <i class="bi bi-envelope"></i> Reply via Email
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<style>
    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        border-left: 4px solid var(--primary-green);
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: 0.3s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .stat-number {
        font-size: 2.2rem;
        font-weight: 700;
    }
    .stat-label {
        color: #666;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-icon {
        font-size: 2rem;
        opacity: 0.5;
    }
    .btn-primary-green {
        background: var(--primary-green);
        color: white;
        border: none;
        transition: 0.3s;
    }
    .btn-primary-green:hover {
        background: #155724;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(31, 99, 50, 0.3);
    }
    .message-content {
        word-wrap: break-word;
    }
    .table-warning {
        background-color: #fff3cd !important;
    }
    .table-warning:hover {
        background-color: #ffe69b !important;
    }
</style>

<script>
// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const closeButton = alert.querySelector('.btn-close');
            if (closeButton) {
                closeButton.click();
            }
        }, 5000);
    });
});
</script>

<?php include 'footer.php'; ?>