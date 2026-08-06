<?php
// admin/dashboard.php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
require_once '../include/functions.php';

$settings = getSettings($pdo);
$contact_count = getContactCount($pdo);
$package_count = $pdo->query("SELECT COUNT(*) FROM packages")->fetchColumn();
$gallery_count = $pdo->query("SELECT COUNT(*) FROM gallery WHERE active = 1")->fetchColumn();

include 'header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-xl-2 mb-4">
            <div class="card text-white bg-primary shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Packages</h5>
                    <h2><?php echo $package_count; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-xl-2 mb-4">
            <div class="card text-white bg-success shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Gallery Images</h5>
                    <h2><?php echo $gallery_count; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-xl-2 mb-4">
            <div class="card text-white bg-warning shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Contact Messages</h5>
                    <h2><?php echo $contact_count; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5>Recent Activity</h5>
                </div>
                <div class="card-body">
                    <p>Welcome to the admin dashboard!</p>
                    <ul>
                        <li>Manage packages, gallery, and page content</li>
                        <li>View and respond to contact messages</li>
                        <li>Update website settings</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5>Quick Links</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="package_edit.php" class="btn btn-outline-primary">Manage Packages</a>
                        <a href="gallery_edit.php" class="btn btn-outline-success">Manage Gallery</a>
                        <a href="contacts.php" class="btn btn-outline-warning">View Contacts</a>
                        <a href="pages.php" class="btn btn-outline-info">Edit Pages</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>