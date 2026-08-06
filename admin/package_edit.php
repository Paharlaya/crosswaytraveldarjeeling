<?php
// admin/package_edit.php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
require_once '../include/functions.php';

$package = null;
$is_edit = false;
$error = '';
$success = '';
$new_package_id = null;

// If we have a saved ID in session (from POST-Redirect-Get pattern)
if(isset($_SESSION['saved_package_id'])) {
    $saved_id = $_SESSION['saved_package_id'];
    unset($_SESSION['saved_package_id']);
    
    // Redirect to the edit page with the saved ID
    header('Location: ?id=' . $saved_id);
    exit;
}

// Handle Delete action
if(isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    
    // Get image path first to delete the file
    $stmt = $pdo->prepare("SELECT image_path FROM packages WHERE id = ?");
    $stmt->execute([$delete_id]);
    $pkg = $stmt->fetch();
    
    if($pkg && !empty($pkg['image_path'])) {
        if(file_exists('../' . $pkg['image_path'])) {
            unlink('../' . $pkg['image_path']);
        }
    }
    
    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM packages WHERE id = ?");
    $stmt->execute([$delete_id]);
    
    // Store success message in session
    $_SESSION['success_message'] = 'Package deleted successfully!';
    
    // Redirect to the same page without the delete parameter
    header('Location: package_edit.php');
    exit;
}

// Handle Toggle Featured action
if(isset($_GET['toggle_featured'])) {
    $toggle_id = intval($_GET['toggle_featured']);
    
    $stmt = $pdo->prepare("UPDATE packages SET featured = NOT featured WHERE id = ?");
    $stmt->execute([$toggle_id]);
    
    $_SESSION['success_message'] = 'Package featured status updated!';
    header('Location: package_edit.php?id=' . $toggle_id);
    exit;
}

if(isset($_GET['id'])) {
    $is_edit = true;
    $stmt = $pdo->prepare("SELECT * FROM packages WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $package = $stmt->fetch();
    if(!$package) {
        header('Location: package_edit.php');
        exit;
    }
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $duration = trim($_POST['duration']);
    $destination = trim($_POST['destination']);
    $inclusions = trim($_POST['inclusions']);
    $exclusions = trim($_POST['exclusions']);
    $availability = isset($_POST['availability']) ? 1 : 0;
    $featured = isset($_POST['featured']) ? 1 : 0;
    $remove_image = isset($_POST['remove_image']) ? 1 : 0;

    // Validate required fields
    if(empty($name) || empty($description) || empty($price) || empty($duration) || empty($destination)) {
        $error = 'Please fill in all required fields (*).';
    }

    // Handle file upload
    $image_path = null;
    
    if(!$error) {
        // If editing, keep existing image path
        if($is_edit && $package) {
            $image_path = $package['image_path'];
        }
        
        // Check if we should remove image
        if($remove_image && $is_edit && $package && !empty($package['image_path'])) {
            if(file_exists('../' . $package['image_path'])) {
                unlink('../' . $package['image_path']);
            }
            $image_path = null;
        }
        
        // Handle new image upload
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = '../images/packages/';
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES['image']['type'];
            if(!in_array($file_type, $allowed_types)) {
                $error = 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.';
            } else {
                // Generate unique filename
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = time() . '_' . uniqid() . '.' . $ext;
                $target_path = $upload_dir . $filename;
                
                if(move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    $image_path = 'images/packages/' . $filename;
                    
                    // Delete old image if editing and not removing
                    if($is_edit && $package && !empty($package['image_path']) && !$remove_image) {
                        if(file_exists('../' . $package['image_path'])) {
                            unlink('../' . $package['image_path']);
                        }
                    }
                } else {
                    $error = 'Failed to upload image.';
                }
            }
        }
        
        // If no image uploaded and not editing, keep as NULL
        if(!$is_edit && empty($image_path) && (!isset($_FILES['image']) || $_FILES['image']['error'] != 0)) {
            $image_path = null;
        }
    }

    if(!$error) {
        try {
            if($is_edit && isset($_GET['id'])) {
                // Update existing package
                $stmt = $pdo->prepare("UPDATE packages SET 
                    name = ?, description = ?, price = ?, duration = ?, 
                    destination = ?, image_path = ?, inclusions = ?, exclusions = ?, 
                    availability = ?, featured = ? WHERE id = ?");
                $stmt->execute([$name, $description, $price, $duration, $destination, 
                              $image_path, $inclusions, $exclusions, $availability, $featured, $_GET['id']]);
                
                // Use PRG pattern - store success in session and redirect to GET
                $_SESSION['success_message'] = 'Package updated successfully!';
                $_SESSION['edited_id'] = $_GET['id'];
                header('Location: ?id=' . $_GET['id']);
                exit;
            } else {
                // Add new package - ensure image_path is NULL if no image
                $final_image_path = empty($image_path) ? null : $image_path;
                
                $stmt = $pdo->prepare("INSERT INTO packages 
                    (name, description, price, duration, destination, image_path, inclusions, exclusions, availability, featured) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $description, $price, $duration, $destination, 
                              $final_image_path, $inclusions, $exclusions, $availability, $featured]);
                $new_id = $pdo->lastInsertId();
                
                // Use PRG pattern - store success and new ID in session
                $_SESSION['success_message'] = 'Package added successfully!';
                $_SESSION['saved_package_id'] = $new_id;
                header('Location: package_edit.php');
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
    
    // If we just edited a package, refresh the data
    if(isset($_SESSION['edited_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM packages WHERE id = ?");
        $stmt->execute([$_SESSION['edited_id']]);
        $package = $stmt->fetch();
        unset($_SESSION['edited_id']);
        $is_edit = true;
    }
}

// Get all packages for the table
$all_packages = $pdo->query("SELECT * FROM packages ORDER BY created_at DESC")->fetchAll();

// Get unread count for sidebar
$unread_count = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status = 'unread'")->fetchColumn();

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="brand-font" style="color: var(--primary-blue);">
        <?php echo $is_edit ? 'Edit Package' : 'Add New Package'; ?>
    </h2>
    <div class="d-flex gap-2 flex-wrap">
        <a href="package_edit.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
        <?php if($is_edit): ?>
        <a href="package_edit.php" class="btn btn-primary-green">
            <i class="bi bi-plus-circle"></i> Add New
        </a>
        <?php endif; ?>
    </div>
</div>

<?php if($success): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i> <?php echo $success; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if($error): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i> <?php echo $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <!-- Form Section -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-blue), var(--primary-green)); color: white;">
                <h5 class="mb-0"><i class="bi <?php echo $is_edit ? 'bi-pencil-square' : 'bi-plus-circle'; ?> me-2"></i> <?php echo $is_edit ? 'Edit Package Details' : 'Add New Package'; ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" id="packageForm">
                    <!-- Image Upload Field -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Package Image <?php echo $is_edit ? '(optional - leave empty to keep current)' : '(optional)'; ?></label>
                        <input type="file" name="image" class="form-control" accept="image/*" id="imageInput">
                        <small class="text-muted">Recommended: 800x600px. JPG, PNG, GIF, WEBP</small>
                    </div>
                    
                    <?php if($is_edit && $package && !empty($package['image_path'])): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Current Image</label>
                        <div>
                            <img src="../<?php echo $package['image_path']; ?>?t=<?php echo time(); ?>" alt="Current" style="max-height: 150px; border-radius: 8px; border: 3px solid var(--primary-green);">
                        </div>
                        <div class="form-check mt-2">
                            <input type="checkbox" name="remove_image" class="form-check-input" id="removeImage">
                            <label class="form-check-label text-danger" for="removeImage">
                                <i class="bi bi-trash"></i> Remove current image
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Package Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($package['name'] ?? ''); ?>" id="nameInput" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Destination <span class="text-danger">*</span></label>
                            <input type="text" name="destination" class="form-control" value="<?php echo htmlspecialchars($package['destination'] ?? ''); ?>" id="destinationInput" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" id="descriptionInput" required><?php echo htmlspecialchars($package['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Price (Rs) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $package['price'] ?? ''; ?>" id="priceInput" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Duration <span class="text-danger">*</span></label>
                            <input type="text" name="duration" class="form-control" value="<?php echo $package['duration'] ?? ''; ?>" placeholder="e.g., 5 Days" id="durationInput" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <div class="form-check mt-2">
                                <input type="checkbox" name="availability" class="form-check-input" id="availabilityInput" <?php echo ($package['availability'] ?? 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label">Available</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="featured" class="form-check-input" id="featuredInput" <?php echo ($package['featured'] ?? 0) ? 'checked' : ''; ?>>
                                <label class="form-check-label">Featured</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Inclusions</label>
                        <textarea name="inclusions" class="form-control" rows="2" id="inclusionsInput"><?php echo htmlspecialchars($package['inclusions'] ?? ''); ?></textarea>
                        <small class="text-muted">Separate items with commas for better display</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Exclusions</label>
                        <textarea name="exclusions" class="form-control" rows="2" id="exclusionsInput"><?php echo htmlspecialchars($package['exclusions'] ?? ''); ?></textarea>
                        <small class="text-muted">Separate items with commas for better display</small>
                    </div>
                    
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary-green">
                            <i class="bi bi-save"></i> <?php echo $is_edit ? 'Update Package' : 'Add Package'; ?>
                        </button>
                        
                        <?php if($is_edit): ?>
                        <a href="package_edit.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Live Preview Section -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-blue), var(--primary-green)); color: white;">
                <h5 class="mb-0"><i class="bi bi-eye me-2"></i> Live Preview</h5>
            </div>
            <div class="card-body">
                <div id="previewContainer">
                    <!-- Preview will be inserted here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Existing Packages Table -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background: #6c757d; color: white; display: flex; justify-content: space-between; align-items: center;">
                <h5 class="mb-0"><i class="bi bi-boxes me-2"></i> All Packages</h5>
                <span class="badge bg-light text-dark">Total: <?php echo count($all_packages); ?></span>
            </div>
            <div class="card-body">
                <?php if(empty($all_packages)): ?>
                <div class="text-center py-4">
                    <i class="bi bi-box fs-1 text-muted d-block mb-3"></i>
                    <p class="text-muted">No packages found. Add your first package above!</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Destination</th>
                                <th>Duration</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($all_packages as $pkg): ?>
                            <tr <?php echo ($is_edit && $package && $pkg['id'] == $package['id']) ? 'class="table-primary"' : ''; ?>>
                                <td><?php echo $pkg['id']; ?></td>
                                <td>
                                    <?php if(!empty($pkg['image_path'])): ?>
                                    <img src="../<?php echo $pkg['image_path']; ?>?t=<?php echo time(); ?>" alt="<?php echo htmlspecialchars($pkg['name']); ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                    <?php else: ?>
                                    <div style="width: 60px; height: 60px; background: #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-image text-muted fs-4"></i>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($pkg['name']); ?></strong>
                                    <?php if(strlen($pkg['description']) > 50): ?>
                                    <br><small class="text-muted"><?php echo substr($pkg['description'], 0, 50) . '...'; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($pkg['destination']); ?></td>
                                <td><?php echo $pkg['duration']; ?></td>
                                <td><strong>₹<?php echo number_format($pkg['price'], 2); ?></strong></td>
                                <td>
                                    <span class="badge <?php echo $pkg['availability'] ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $pkg['availability'] ? 'Available' : 'Unavailable'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($pkg['featured']): ?>
                                    <span class="badge bg-warning text-dark">★ Featured</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Not Featured</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?id=<?php echo $pkg['id']; ?>" class="btn btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="?toggle_featured=<?php echo $pkg['id']; ?>" class="btn btn-info text-white" title="Toggle Featured">
                                            <i class="bi bi-star"></i>
                                        </a>
                                        <a href="?delete=<?php echo $pkg['id']; ?>" class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this package?')">
                                            <i class="bi bi-trash"></i>
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

<!-- Preview Template -->
<template id="previewTemplate">
    <div class="package-preview">
        <div class="preview-image-wrapper mb-3" style="background: #f8f9fa; border-radius: 8px; overflow: hidden; min-height: 200px; display: flex; align-items: center; justify-content: center;">
            <img id="previewImage" src="" alt="Preview" style="max-height: 250px; width: 100%; object-fit: cover; display: none;">
            <div id="previewPlaceholder" style="padding: 40px 20px; color: #6c757d; text-align: center;">
                <i class="bi bi-image fs-1 d-block mb-2"></i>
                <h6>No image selected</h6>
                <small class="text-muted">Upload an image to preview</small>
            </div>
        </div>
        <div class="preview-details text-start">
            <h5 id="previewName" class="mb-2" style="color: var(--primary-blue);">Package Name</h5>
            <p id="previewDescription" class="text-muted mb-2">Description</p>
            <div class="d-flex justify-content-between align-items-center">
                <span id="previewDestination" class="badge bg-info">Destination</span>
                <span id="previewPrice" class="badge bg-success">Price</span>
            </div>
            <div class="mt-2">
                <small class="text-muted">Duration: <span id="previewDuration">0 Days</span></small>
            </div>
            <div class="mt-1">
                <span id="previewAvailability" class="badge">Status</span>
                <span id="previewFeatured" class="badge bg-warning text-dark" style="display: none;">★ Featured</span>
            </div>
        </div>
    </div>
</template>

<style>
    .package-preview {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
    }
    .preview-details {
        padding: 15px;
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
    .card .card-header {
        border-radius: 12px 12px 0 0 !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const previewContainer = document.getElementById('previewContainer');
    const template = document.getElementById('previewTemplate');
    
    const imageInput = document.getElementById('imageInput');
    const nameInput = document.getElementById('nameInput');
    const descriptionInput = document.getElementById('descriptionInput');
    const destinationInput = document.getElementById('destinationInput');
    const priceInput = document.getElementById('priceInput');
    const durationInput = document.getElementById('durationInput');
    const availabilityInput = document.getElementById('availabilityInput');
    const featuredInput = document.getElementById('featuredInput');
    const removeImageCheck = document.getElementById('removeImage');
    
    function updatePreview() {
        const previewContent = template.content.cloneNode(true);
        
        const previewImage = previewContent.getElementById('previewImage');
        const previewPlaceholder = previewContent.getElementById('previewPlaceholder');
        const previewName = previewContent.getElementById('previewName');
        const previewDescription = previewContent.getElementById('previewDescription');
        const previewDestination = previewContent.getElementById('previewDestination');
        const previewPrice = previewContent.getElementById('previewPrice');
        const previewDuration = previewContent.getElementById('previewDuration');
        const previewAvailability = previewContent.getElementById('previewAvailability');
        const previewFeatured = previewContent.getElementById('previewFeatured');
        
        // Update text fields
        previewName.textContent = nameInput.value.trim() || 'Untitled Package';
        previewDescription.textContent = descriptionInput.value.trim() || 'No description provided';
        previewDestination.textContent = destinationInput.value.trim() || 'Unknown Destination';
        previewPrice.textContent = '₹' + (parseFloat(priceInput.value) || 0).toFixed(2);
        previewDuration.textContent = durationInput.value.trim() || '0 Days';
        
        // Update availability
        if (availabilityInput.checked) {
            previewAvailability.textContent = 'Available';
            previewAvailability.className = 'badge bg-success';
        } else {
            previewAvailability.textContent = 'Unavailable';
            previewAvailability.className = 'badge bg-danger';
        }
        
        // Update featured
        previewFeatured.style.display = featuredInput.checked ? 'inline-block' : 'none';
        
        // Update image
        let hasImage = false;
        
        if (imageInput.files && imageInput.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
                previewPlaceholder.style.display = 'none';
            };
            reader.readAsDataURL(imageInput.files[0]);
            hasImage = true;
        }
        
        <?php if($is_edit && $package && !empty($package['image_path'])): ?>
        if (!hasImage && !(removeImageCheck && removeImageCheck.checked)) {
            previewImage.src = '../<?php echo $package['image_path']; ?>?t=<?php echo time(); ?>';
            previewImage.style.display = 'block';
            previewPlaceholder.style.display = 'none';
            hasImage = true;
        }
        <?php endif; ?>
        
        if (!hasImage || (removeImageCheck && removeImageCheck.checked)) {
            previewImage.style.display = 'none';
            previewPlaceholder.style.display = 'block';
        }
        
        previewContainer.innerHTML = '';
        previewContainer.appendChild(previewContent);
    }
    
    // Add event listeners
    [nameInput, descriptionInput, destinationInput, priceInput, durationInput, availabilityInput, featuredInput].forEach(input => {
        if (input) {
            input.addEventListener('input', updatePreview);
            input.addEventListener('change', updatePreview);
        }
    });
    
    if (imageInput) imageInput.addEventListener('change', updatePreview);
    if (removeImageCheck) removeImageCheck.addEventListener('change', updatePreview);
    
    updatePreview();
});
</script>

<?php include 'footer.php'; ?>