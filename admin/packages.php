<?php
// admin/packages.php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
require_once '../include/functions.php';

// Handle package deletion
if(isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM packages WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: packages.php?msg=deleted');
    exit;
}

// Toggle featured status
if(isset($_GET['toggle_featured'])) {
    $stmt = $pdo->prepare("UPDATE packages SET featured = NOT featured WHERE id = ?");
    $stmt->execute([$_GET['toggle_featured']]);
    header('Location: packages.php');
    exit;
}

// Get all packages
$packages = getPackages($pdo);

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Manage Packages</h2>
    <a href="package_edit.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Package
    </a>
</div>

<?php if(isset($_GET['msg'])): ?>
<div class="alert alert-success alert-dismissible">
    <?php 
        if($_GET['msg'] == 'deleted') echo 'Package deleted successfully!';
        if($_GET['msg'] == 'saved') echo 'Package saved successfully!';
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Destination</th>
                        <th>Duration</th>
                        <th>Price</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($packages as $package): ?>
                    <tr>
                        <td><?php echo $package['id']; ?></td>
                        <td><?php echo htmlspecialchars($package['name']); ?></td>
                        <td><?php echo htmlspecialchars($package['destination']); ?></td>
                        <td><?php echo $package['duration']; ?></td>
                        <td>$<?php echo number_format($package['price'], 2); ?></td>
                        <td>
                            <a href="?toggle_featured=<?php echo $package['id']; ?>" class="btn btn-sm <?php echo $package['featured'] ? 'btn-warning' : 'btn-secondary'; ?>">
                                <?php echo $package['featured'] ? '★' : '☆'; ?>
                            </a>
                        </td>
                        <td>
                            <a href="package_edit.php?id=<?php echo $package['id']; ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?delete=<?php echo $package['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>