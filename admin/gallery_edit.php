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
$new_image_id = null;

// If we have a saved ID in session (from POST-Redirect-Get pattern)
if(isset($_SESSION['saved_gallery_id'])) {
    $saved_id = $_SESSION['saved_gallery_id'];
    unset($_SESSION['saved_gallery_id']);
    
    // Redirect to the edit page with the saved ID
    header('Location: ?id=' . $saved_id);
    exit;
}

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
                
                // Use PRG pattern - store success in session and redirect to GET
                $_SESSION['success_message'] = 'Image updated successfully!';
                $_SESSION['edited_id'] = $_GET['id'];
                header('Location: ?id=' . $_GET['id']);
                exit;
            } else {
                $stmt = $pdo->prepare("INSERT INTO gallery 
                    (title, description, image_path, category, sort_order, active) 
                    VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $image_path, 
                              $category, $sort_order, $active]);
                $new_id = $pdo->lastInsertId();
                
                // Use PRG pattern - store success and new ID in session
                $_SESSION['success_message'] = 'Image added successfully!';
                $_SESSION['saved_gallery_id'] = $new_id;
                header('Location: gallery_edit.php');
                exit;
            }
        } catch(PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Check for success message from session (PRG pattern)
if(isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
    
    // If we just edited an image, refresh the data
    if(isset($_SESSION['edited_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ?");
        $stmt->execute([$_SESSION['edited_id']]);
        $image = $stmt->fetch();
        unset($_SESSION['edited_id']);
    }
}

// Get all gallery images for the table
$all_images = $pdo->query("SELECT * FROM gallery ORDER BY sort_order, upload_date DESC")->fetchAll();

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo $is_edit ? 'Edit Image' : 'Add New Image'; ?></h2>
    <div>
        <a href="gallery.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Gallery
        </a>
        <?php if($is_edit): ?>
        <a href="gallery_edit.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Image
        </a>
        <?php endif; ?>
    </div>
</div>

<?php if($success): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if($error): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <!-- Form Section -->
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas <?php echo $is_edit ? 'fa-edit' : 'fa-plus-circle'; ?>"></i> <?php echo $is_edit ? 'Edit Image Details' : 'Add New Image'; ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" id="galleryForm">
                    <?php if($is_edit && $image['image_path']): ?>
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div>
                            <img src="../<?php echo $image['image_path']; ?>" alt="Current" style="max-height: 200px; border-radius: 8px; border: 2px solid #dee2e6;">
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Image <?php echo $is_edit ? '(optional - leave empty to keep current)' : '*'; ?></label>
                        <input type="file" name="image" class="form-control" accept="image/*" id="imageInput" <?php echo $is_edit ? '' : 'required'; ?>>
                        <small class="text-muted">Recommended size: 800x600 pixels. Supported formats: JPG, PNG, GIF</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($image['title'] ?? ''); ?>" id="titleInput" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" id="descriptionInput"><?php echo htmlspecialchars($image['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($image['category'] ?? ''); ?>" id="categoryInput" placeholder="e.g., Europe, Asia, Nature">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="<?php echo $image['sort_order'] ?? 0; ?>" id="sortOrderInput" min="0">
                            <small class="text-muted">Lower numbers appear first</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check mt-2">
                                <input type="checkbox" name="active" class="form-check-input" id="activeInput" <?php echo (($image['active'] ?? 1) ? 'checked' : ''); ?>>
                                <label class="form-check-label">Active</label>
                                <br><small class="text-muted">Inactive images won't show on the frontend</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $is_edit ? 'Update Image' : 'Add Image'; ?>
                        </button>
                        
                        <?php if($is_edit): ?>
                        <a href="gallery.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Live Preview Section -->
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-eye"></i> Live Preview</h5>
            </div>
            <div class="card-body">
                <div id="previewContainer" class="text-center">
                    <!-- Preview will be inserted here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Existing Gallery Images Table -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-images"></i> All Gallery Images</h5>
                <span class="badge bg-light text-dark">Total: <?php echo count($all_images); ?></span>
            </div>
            <div class="card-body">
                <?php if(empty($all_images)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-image fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No images in the gallery yet. Add your first image above!</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Sort Order</th>
                                <th>Date Added</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($all_images as $img): ?>
                            <tr <?php echo ($is_edit && $image && $img['id'] == $image['id']) ? 'class="table-primary"' : ''; ?>>
                                <td><?php echo $img['id']; ?></td>
                                <td>
                                    <img src="../<?php echo $img['image_path']; ?>" alt="<?php echo htmlspecialchars($img['title']); ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($img['title']); ?></strong>
                                    <?php if($img['description']): ?>
                                    <br><small class="text-muted"><?php echo truncateText($img['description'], 30); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($img['category']): ?>
                                    <span class="badge bg-info"><?php echo htmlspecialchars($img['category']); ?></span>
                                    <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo $img['active'] ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $img['active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td><?php echo $img['sort_order']; ?></td>
                                <td><?php echo date('M j, Y', strtotime($img['upload_date'])); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?id=<?php echo $img['id']; ?>" class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="gallery.php?delete=<?php echo $img['id']; ?>" class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this image?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <a href="gallery.php?toggle_active=<?php echo $img['id']; ?>" class="btn <?php echo $img['active'] ? 'btn-secondary' : 'btn-success'; ?>" title="<?php echo $img['active'] ? 'Deactivate' : 'Activate'; ?>">
                                            <i class="fas <?php echo $img['active'] ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Preview Template (hidden) -->
<template id="previewTemplate">
    <div class="gallery-preview">
        <div class="preview-image-wrapper mb-3">
            <img id="previewImage" src="" alt="Preview" class="img-fluid rounded" style="max-height: 300px; width: 100%; object-fit: cover;">
        </div>
        <div class="preview-details text-start">
            <h5 id="previewTitle" class="mb-2">Title</h5>
            <p id="previewDescription" class="text-muted mb-2">Description</p>
            <div class="d-flex justify-content-between align-items-center">
                <span id="previewCategory" class="badge bg-info">Category</span>
                <span id="previewStatus" class="badge">Status</span>
            </div>
            <div class="mt-2">
                <small class="text-muted">Sort Order: <span id="previewSortOrder">0</span></small>
            </div>
        </div>
    </div>
</template>

<style>
    .gallery-preview {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
    }
    
    .preview-image-wrapper {
        background: #f8f9fa;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .preview-image-wrapper img {
        transition: all 0.3s ease;
    }
    
    .preview-details {
        padding: 15px;
    }
    
    .placeholder-preview {
        padding: 40px 20px;
        color: #6c757d;
    }
    
    .placeholder-preview i {
        font-size: 48px;
        margin-bottom: 15px;
        display: block;
    }
    
    .placeholder-preview h6 {
        color: #495057;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
    
    .table-primary {
        background-color: #cfe2ff !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('galleryForm');
    const previewContainer = document.getElementById('previewContainer');
    const template = document.getElementById('previewTemplate');
    
    // Get all input elements
    const imageInput = document.getElementById('imageInput');
    const titleInput = document.getElementById('titleInput');
    const descriptionInput = document.getElementById('descriptionInput');
    const categoryInput = document.getElementById('categoryInput');
    const activeInput = document.getElementById('activeInput');
    const sortOrderInput = document.getElementById('sortOrderInput');
    
    // Function to update preview
    function updatePreview() {
        // Clone the template content
        const previewContent = template.content.cloneNode(true);
        
        // Get preview elements
        const previewImage = previewContent.getElementById('previewImage');
        const previewTitle = previewContent.getElementById('previewTitle');
        const previewDescription = previewContent.getElementById('previewDescription');
        const previewCategory = previewContent.getElementById('previewCategory');
        const previewStatus = previewContent.getElementById('previewStatus');
        const previewSortOrder = previewContent.getElementById('previewSortOrder');
        
        // Update title
        const title = titleInput.value.trim() || 'Untitled';
        previewTitle.textContent = title;
        
        // Update description
        const description = descriptionInput.value.trim() || 'No description provided';
        previewDescription.textContent = description;
        
        // Update category
        const category = categoryInput.value.trim();
        if (category) {
            previewCategory.textContent = category;
            previewCategory.style.display = 'inline-block';
        } else {
            previewCategory.style.display = 'none';
        }
        
        // Update status
        const isActive = activeInput.checked;
        if (isActive) {
            previewStatus.textContent = 'Active';
            previewStatus.className = 'badge bg-success';
        } else {
            previewStatus.textContent = 'Inactive';
            previewStatus.className = 'badge bg-danger';
        }
        
        // Update sort order
        const sortOrder = sortOrderInput.value || '0';
        previewSortOrder.textContent = sortOrder;
        
        // Update image
        let hasImage = false;
        
        // Check if there's a file selected
        if (imageInput.files && imageInput.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
                const placeholder = previewImage.parentElement.querySelector('.placeholder-preview');
                if (placeholder) placeholder.remove();
            };
            reader.readAsDataURL(imageInput.files[0]);
            hasImage = true;
        } 
        // If editing and no new file selected, use existing image
        <?php if($is_edit && $image['image_path']): ?>
        if (!hasImage) {
            previewImage.src = '../<?php echo $image['image_path']; ?>';
            previewImage.style.display = 'block';
            hasImage = true;
        }
        <?php endif; ?>
        
        // Show placeholder if no image
        if (!hasImage) {
            previewImage.style.display = 'none';
            const placeholder = document.createElement('div');
            placeholder.className = 'placeholder-preview';
            placeholder.innerHTML = `
                <i class="fas fa-image text-muted"></i>
                <h6>No image selected</h6>
                <small class="text-muted">Upload an image to preview</small>
            `;
            previewImage.parentElement.appendChild(placeholder);
        }
        
        // Update preview container
        previewContainer.innerHTML = '';
        previewContainer.appendChild(previewContent);
    }
    
    // Add event listeners to all inputs
    const inputs = [titleInput, descriptionInput, categoryInput, activeInput, sortOrderInput];
    inputs.forEach(input => {
        if (input) {
            input.addEventListener('input', updatePreview);
            input.addEventListener('change', updatePreview);
        }
    });
    
    if (imageInput) {
        imageInput.addEventListener('change', updatePreview);
    }
    
    // Initial preview
    updatePreview();
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            const closeButton = alert.querySelector('.btn-close');
            if (closeButton) {
                closeButton.click();
            }
        });
    }, 5000);
});
</script>

<?php include 'footer.php'; ?>