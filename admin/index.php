<?php
// admin/index.php - Admin Dashboard
session_start();

// Check if user is logged in
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
require_once '../include/functions.php';

$settings = getSettings($pdo);
$page = getPage($pdo, 'dashboard');

// Get statistics
try {
    // Total packages
    $stmt = $pdo->query("SELECT COUNT(*) FROM packages");
    $total_packages = $stmt->fetchColumn();
    
    // Featured packages
    $stmt = $pdo->query("SELECT COUNT(*) FROM packages WHERE featured = 1");
    $total_featured = $stmt->fetchColumn();
    
    // Total contacts/inquiries
    $stmt = $pdo->query("SELECT COUNT(*) FROM contacts");
    $total_contacts = $stmt->fetchColumn();
    
    // Unread contacts
    $stmt = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status = 'unread'");
    $unread_contacts = $stmt->fetchColumn();
    
    // Total gallery images
    $stmt = $pdo->query("SELECT COUNT(*) FROM gallery");
    $total_gallery = $stmt->fetchColumn();
    
    // Total pages
    $stmt = $pdo->query("SELECT COUNT(*) FROM pages");
    $total_pages = $stmt->fetchColumn();
    
} catch(PDOException $e) {
    $total_packages = 0;
    $total_featured = 0;
    $total_contacts = 0;
    $unread_contacts = 0;
    $total_gallery = 0;
    $total_pages = 0;
}

// Get recent contacts
$recent_contacts = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM contacts ORDER BY submitted_at DESC LIMIT 5");
    $stmt->execute();
    $recent_contacts = $stmt->fetchAll();
} catch(PDOException $e) {
    $recent_contacts = [];
}

// Get recent packages
$recent_packages = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM packages ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $recent_packages = $stmt->fetchAll();
} catch(PDOException $e) {
    $recent_packages = [];
}

// Get admin info
$admin_name = $_SESSION['admin_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard · <?php echo htmlspecialchars($settings['site_name'] ?? 'CrossWay Travel'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,700&family=Inter:opsz,wght@14..32,300..700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-green: #1f6332;
            --primary-blue: #013565;
            --green-light: #e6f0e8;
            --blue-light: #e3edf5;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1e2b2f;
        }
        .brand-font {
            font-family: 'Playfair Display', serif;
        }
        .sidebar {
            min-height: 100vh;
            background: var(--primary-blue);
            color: white;
            padding: 20px 0;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 24px;
            transition: 0.3s;
            border-radius: 8px;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .sidebar .nav-link.active {
            background: var(--primary-green);
            color: white;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
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
            color: var(--primary-blue);
        }
        .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-icon {
            font-size: 2rem;
            color: var(--primary-green);
            opacity: 0.5;
        }
        .content-area {
            padding: 30px;
        }
        .table-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .status-unread { background: #fff3cd; color: #856404; }
        .status-read { background: #d1ecf1; color: #0c5460; }
        .status-replied { background: #d4edda; color: #155724; }
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
        .btn-outline-blue {
            border: 2px solid var(--primary-blue);
            color: var(--primary-blue);
            background: transparent;
            transition: 0.3s;
        }
        .btn-outline-blue:hover {
            background: var(--primary-blue);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(1, 53, 101, 0.3);
        }
        .quick-action-btn {
            padding: 14px 20px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            transition: 0.3s;
        }
        .quick-action-btn:hover {
            transform: translateY(-3px);
        }
        .welcome-text {
            font-size: 1.1rem;
            color: #6c757d;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-green);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .table th {
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }
        .table td {
            vertical-align: middle;
        }
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }
            .content-area {
                padding: 15px;
            }
            .stat-number {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="text-center mb-4 brand-font" style="color: white;">
                    <i class="bi bi-compass-fill" style="color: var(--primary-green);"></i> CrossWay
                </h4>
                <div class="text-center mb-4">
                    <small style="opacity: 0.7;">Admin Panel</small>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="index.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                     <a class="nav-link" href="about.php">
                        <i class="bi bi-images"></i> About
                    </a>
                    <a class="nav-link" href="package_edit.php">
                        <i class="bi bi-box-seam"></i> Packages
                    </a>
                    <a class="nav-link" href="sightseeing_edit.php">
                        <i class="bi bi-map"></i> Sightseeing
                    </a>
                    <a class="nav-link" href="gallery_edit.php">
                        <i class="bi bi-images"></i> Gallery
                    </a>
                    <a class="nav-link" href="pages.php">
                        <i class="bi bi-file-text"></i> Pages
                    </a>
                    <a class="nav-link" href="contacts.php">
                        <i class="bi bi-envelope"></i> Contacts
                        <?php if($unread_contacts > 0): ?>
                        <span class="badge bg-danger ms-2 rounded-pill"><?php echo $unread_contacts; ?></span>
                        <?php endif; ?>
                    </a>
                    <a class="nav-link" href="settings.php">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                    <a class="nav-link" href="../index.php" style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
                        <i class="bi bi-box-arrow-right"></i> View Site
                    </a>
                    <a class="nav-link" href="logout.php" style="color: #ff6b6b;">
                        <i class="bi bi-power"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 content-area">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="brand-font" style="color: var(--primary-blue);">Dashboard</h2>
                        <p class="welcome-text">Welcome back, <strong><?php echo htmlspecialchars($admin_name); ?></strong>!</p>
                    </div>
                    <div>
                        <span class="badge bg-tea-green p-2 px-3 rounded-pill">
                            <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($admin_name); ?>
                        </span>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="stat-number"><?php echo $total_packages; ?></div>
                                    <div class="stat-label">Total Packages</div>
                                </div>
                                <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
                            </div>
                            <small class="text-muted"><?php echo $total_featured; ?> featured</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="stat-number"><?php echo $total_contacts; ?></div>
                                    <div class="stat-label">Total Inquiries</div>
                                </div>
                                <div class="stat-icon"><i class="bi bi-envelope"></i></div>
                            </div>
                            <small class="text-muted"><?php echo $unread_contacts; ?> unread</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="stat-number"><?php echo $total_gallery; ?></div>
                                    <div class="stat-label">Gallery Images</div>
                                </div>
                                <div class="stat-icon"><i class="bi bi-images"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="stat-number"><?php echo $total_pages; ?></div>
                                    <div class="stat-label">Pages</div>
                                </div>
                                <div class="stat-icon"><i class="bi bi-file-text"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Contacts -->
                <div class="table-card">
                    <h5 class="brand-font" style="color: var(--primary-blue);">
                        <i class="bi bi-envelope me-2"></i> Recent Inquiries
                    </h5>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_contacts)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                        No inquiries yet.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach($recent_contacts as $contact): ?>
                                <tr>
                                    <td><?php echo $contact['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($contact['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                    <td><?php echo htmlspecialchars($contact['subject']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $contact['status']; ?>">
                                            <?php echo ucfirst($contact['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($contact['submitted_at'])); ?></td>
                                    <td>
                                        <a href="contacts.php?id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php if(count($recent_contacts) > 0): ?>
                        <div class="text-end mt-2">
                            <a href="contacts.php" class="btn btn-sm btn-outline-secondary">
                                View All <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Packages -->
                <div class="table-card mt-4">
                    <h5 class="brand-font" style="color: var(--primary-blue);">
                        <i class="bi bi-box-seam me-2"></i> Recent Packages
                    </h5>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Destination</th>
                                    <th>Price</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_packages)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-box fs-4 d-block mb-2"></i>
                                        No packages added yet.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach($recent_packages as $package): ?>
                                <tr>
                                    <td><?php echo $package['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($package['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($package['destination']); ?></td>
                                    <td>₹<?php echo number_format($package['price'], 2); ?></td>
                                    <td><?php echo $package['duration']; ?></td>
                                    <td>
                                        <span class="badge <?php echo $package['availability'] ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo $package['availability'] ? 'Available' : 'Unavailable'; ?>
                                        </span>
                                        <?php if($package['featured']): ?>
                                        <span class="badge bg-warning text-dark">★ Featured</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="package_edit.php?id=<?php echo $package['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php if(count($recent_packages) > 0): ?>
                        <div class="text-end mt-2">
                            <a href="package_edit.php" class="btn btn-sm btn-outline-secondary">
                                View All <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row g-3 mt-4">
                    <div class="col-md-3 col-sm-6">
                        <a href="package_edit.php" class="quick-action-btn btn-primary-green">
                            <i class="bi bi-plus-circle"></i> Add Package
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="sightseeing_edit.php" class="quick-action-btn btn-outline-blue">
                            <i class="bi bi-plus-circle"></i> Add Sightseeing
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="contacts.php" class="quick-action-btn btn-outline-blue">
                            <i class="bi bi-envelope"></i> View Inquiries
                            <?php if($unread_contacts > 0): ?>
                            <span class="badge bg-danger ms-1 rounded-pill"><?php echo $unread_contacts; ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="gallery.php" class="quick-action-btn btn-outline-blue">
                            <i class="bi bi-images"></i> Manage Gallery
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>