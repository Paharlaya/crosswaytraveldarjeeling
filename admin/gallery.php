<?php
// admin/gallery_edit.php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
require_once '../include/functions.php';

$image = null;
$is_edit = false;
$error = '';
$success = '';

if(isset($_GET['id'])) {
    $is_edit = true;
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $image = $stmt->fetch();
    if(!$image) {
        header('Location: gallery.php');
        exit;
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $sort_order = intval($_POST['sort_order'] ?? 0);
    $active = isset($_POST['active']) ? 1 : 0;

    // Handle file upload
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../images/gallery/';
        if(!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $target_path = $upload_dir . $filename;
        
        if(move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = 'images/gallery/' . $filename;
            // Delete old image if editing
            if($is_edit && $image['image_path'] && file_exists('../' . $image['image_path'])) {
                unlink('../' . $image['image_path']);
            }
        } else {
            $error = 'Failed to upload image.';
        }
    } else {
        $image_path = $is_edit ? $image['image_path'] : '';
    }

    if(!$error) {
        try {
            if($is_edit) {
                $stmt = $pdo->prepare("UPDATE gallery SET 
                    title = ?, description = ?, image_path = ?, 
                    category = ?, sort_order = ?, active = ? WHERE id = ?");
                $stmt->execute([$title, $description, $image_path, 
                              $category, $sort_order, $active, $_GET['id']]);
                $success = 'Image updated successfully!';
                // Refresh data
                $stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $image = $stmt->fetch();
            } else {
                $stmt = $pdo->prepare("INSERT INTO gallery 
                    (title, description, image_path, category, sort_order, active) 
                    VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $image_path, 
                              $category, $sort_order, $active]);
                $success = 'Image added successfully!';
                header('Location: gallery.php?msg=saved');
                exit;
            }
        } catch(PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo $is_edit ? 'Edit Image' : 'Add New Image'; ?></h2>
    <a href="gallery.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
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
        <form method="POST" enctype="multipart/form-data">
            <?php if($is_edit && $image['image_path']): ?>
            <div class="mb-3">
                <label class="form-label">Current Image</label>
                <div>
                    <img src="../<?php echo $image['image_path']; ?>" alt="Current" style="max-height: 200px;">
                </div>
            </div>
            <?php endif; ?>
            <div class="mb-3">
                <label class="form-label">Image <?php echo $is_edit ? '(optional)' : '*'; ?></label>
                <input type="file" name="image" class="form-control" accept="image/*" <?php echo $is_edit ? '' : 'required'; ?>>
            </div>
            <div class="mb-3">
                <label class="form-label">Title *</label>
                <input type="text" name="title" class="form-control" value="<?php echo $image['title'] ?? ''; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2"><?php echo $image['description'] ?? ''; ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Category</label>
                <input type="text" name="category" class="form-control" value="<?php echo $image['category'] ?? ''; ?>">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="<?php echo $image['sort_order'] ?? 0; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="active" class="form-check-input" <?php echo (($image['active'] ?? 1) ? 'checked' : ''); ?>>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?php echo $is_edit ? 'Update' : 'Add'; ?> Image
            </button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>