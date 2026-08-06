<?php
// admin/settings.php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
require_once '../include/functions.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        foreach($_POST as $key => $value) {
            if(strpos($key, 'setting_') === 0) {
                $setting_key = substr($key, 8);
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([trim($value), $setting_key]);
            }
        }
        $success = 'Settings updated successfully!';
        // Refresh settings
        $settings = getSettings($pdo);
    } catch(PDOException $e) {
        $error = 'An error occurred.';
    }
} else {
    $settings = getSettings($pdo);
}

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Website Settings</h2>
</div>

<?php if($success): ?>
<div class="alert alert-success alert-dismissible">
    <?php echo $success; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if($error): ?>
<div class="alert alert-danger alert-dismissible">
    <?php echo $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Site Name</label>
                    <input type="text" name="setting_site_name" class="form-control" value="<?php echo $settings['site_name'] ?? ''; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Site Email</label>
                    <input type="email" name="setting_site_email" class="form-control" value="<?php echo $settings['site_email'] ?? ''; ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="setting_site_phone" class="form-control" value="<?php echo $settings['site_phone'] ?? ''; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="setting_address" class="form-control" value="<?php echo $settings['address'] ?? ''; ?>">
                </div>
            </div>
            <hr>
            <h5>Social Media Links</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Facebook</label>
                    <input type="url" name="setting_facebook" class="form-control" value="<?php echo $settings['facebook'] ?? ''; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Twitter</label>
                    <input type="url" name="setting_twitter" class="form-control" value="<?php echo $settings['twitter'] ?? ''; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Instagram</label>
                    <input type="url" name="setting_instagram" class="form-control" value="<?php echo $settings['instagram'] ?? ''; ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Settings
            </button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>