<?php
// admin/header.php - Unified Admin Header

// Get unread count only once at the top
$unread_count = 0;

// Check if $pdo is defined (it should be from the parent file)
if(isset($pdo)) {
    try {
        // Check if contacts table exists
        $table_check = $pdo->query("SHOW TABLES LIKE 'contacts'");
        if($table_check->rowCount() > 0) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status = 'unread'");
            $unread_count = $stmt->fetchColumn();
        }
    } catch(PDOException $e) {
        $unread_count = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel · CrossWay Travel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .content-area {
            padding: 30px;
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
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        .card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .card-header {
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
        }
        .btn-primary {
            background: var(--primary-green);
            border: none;
            transition: 0.3s;
        }
        .btn-primary:hover {
            background: #155724;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(31, 99, 50, 0.3);
        }
        .btn-warning {
            color: #1a1a2e;
        }
        .btn-warning:hover {
            transform: translateY(-2px);
        }
        .btn-danger:hover {
            transform: translateY(-2px);
        }
        .btn-info:hover {
            transform: translateY(-2px);
        }
        .form-control {
            border-radius: 8px;
            border-color: #e0e0e0;
            padding: 10px 14px;
            transition: 0.3s;
        }
        .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(31, 99, 50, 0.15);
        }
        .alert {
            border-radius: 12px;
            border: none;
        }
        .alert-success {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            color: #1f6332;
            border-left: 4px solid #1f6332;
        }
        .alert-danger {
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            color: #c62828;
            border-left: 4px solid #c62828;
        }
        .badge.bg-success {
            background: var(--primary-green) !important;
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
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="about.php">
                        <i class="bi bi-info-circle"></i> About
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'package_edit.php' ? 'active' : ''; ?>" href="package_edit.php">
                        <i class="bi bi-box-seam"></i> Packages
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'sightseeing_edit.php' ? 'active' : ''; ?>" href="sightseeing_edit.php">
                        <i class="bi bi-map"></i> Sightseeing
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'gallery_edit.php' ? 'active' : ''; ?>" href="gallery_edit.php">
                        <i class="bi bi-images"></i> Gallery
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'pages.php' ? 'active' : ''; ?>" href="pages.php">
                        <i class="bi bi-file-text"></i> Pages
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contacts.php' ? 'active' : ''; ?>" href="contacts.php">
                        <i class="bi bi-envelope"></i> Contacts
                        
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
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