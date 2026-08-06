<?php
// admin/sightseeing_edit.php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
require_once '../include/functions.php';

$sightseeing = null;
$is_edit = false;
$error = '';
$success = '';

// Handle Delete action
if(isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    
    $stmt = $pdo->prepare("DELETE FROM sightseeing WHERE id = ?");
    $stmt->execute([$delete_id]);
    
    $_SESSION['success_message'] = 'Sightseeing destination deleted successfully!';
    header('Location: sightseeing_edit.php');
    exit;
}

if(isset($_GET['id'])) {
    $is_edit = true;
    $stmt = $pdo->prepare("SELECT * FROM sightseeing WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $sightseeing = $stmt->fetch();
    if(!$sightseeing) {
        header('Location: sightseeing_edit.php');
        exit;
    }
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $icon = trim($_POST['icon']);
    $badge = trim($_POST['badge']);
    $color = trim($_POST['color']);
    $points = trim($_POST['points']);
    $sort_order = intval($_POST['sort_order'] ?? 0);
    $active = isset($_POST['active']) ? 1 : 0;

    // Validate required fields
    if(empty($title) || empty($points)) {
        $error = 'Please fill in all required fields (*).';
    }

    if(!$error) {
        try {
            if($is_edit && isset($_GET['id'])) {
                // Update existing
                $stmt = $pdo->prepare("UPDATE sightseeing SET 
                    title = ?, icon = ?, badge = ?, color = ?, 
                    points = ?, sort_order = ?, active = ? 
                    WHERE id = ?");
                $stmt->execute([$title, $icon, $badge, $color, $points, $sort_order, $active, $_GET['id']]);
                
                $_SESSION['success_message'] = 'Sightseeing destination updated successfully!';
                header('Location: sightseeing_edit.php?id=' . $_GET['id']);
                exit;
            } else {
                // Insert new
                $stmt = $pdo->prepare("INSERT INTO sightseeing 
                    (title, icon, badge, color, points, sort_order, active) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $icon, $badge, $color, $points, $sort_order, $active]);
                $new_id = $pdo->lastInsertId();
                
                $_SESSION['success_message'] = 'Sightseeing destination added successfully!';
                header('Location: sightseeing_edit.php?id=' . $new_id);
                exit;
            }
        } catch(PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Check for success message from session
if(isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
    
    // Refresh data if editing
    if(isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM sightseeing WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $sightseeing = $stmt->fetch();
        $is_edit = true;
    }
}

// Get all sightseeing for the table
$all_sightseeing = $pdo->query("SELECT * FROM sightseeing ORDER BY sort_order ASC")->fetchAll();

// Available icons with labels
$available_icons = [
    'fa-mountain' => '🏔️ Mountain',
    'fa-sun' => '☀️ Sun',
    'fa-map-signs' => '🗺️ Map Signs',
    'fa-tree' => '🌳 Tree',
    'fa-water' => '💧 Water',
    'fa-cloud-sun' => '⛅ Cloud & Sun',
    'fa-city' => '🏙️ City',
    'fa-road' => '🛣️ Road',
    'fa-hiking' => '🥾 Hiking',
    'fa-camping' => '🏕️ Camping',
    'fa-skiing' => '⛷️ Skiing',
    'fa-swimmer' => '🏊 Swimmer',
    'fa-bicycle' => '🚴 Bicycle',
    'fa-umbrella-beach' => '🏖️ Beach',
    'fa-ship' => '🚢 Ship',
    'fa-plane' => '✈️ Plane',
    'fa-train' => '🚂 Train',
    'fa-bus' => '🚌 Bus',
    'fa-taxi' => '🚕 Taxi',
    'fa-walking' => '🚶 Walking',
    'fa-heart' => '❤️ Heart',
    'fa-star' => '⭐ Star',
    'fa-camera' => '📷 Camera',
    'fa-video' => '🎥 Video',
    'fa-music' => '🎵 Music',
    'fa-palette' => '🎨 Palette',
    'fa-paw' => '🐾 Paw',
    'fa-leaf' => '🍃 Leaf',
    'fa-flower' => '🌸 Flower',
    'fa-gem' => '💎 Gem',
    'fa-crown' => '👑 Crown',
    'fa-flag' => '🚩 Flag',
    'fa-fire' => '🔥 Fire',
    'fa-bolt' => '⚡ Bolt',
    'fa-moon' => '🌙 Moon',
    'fa-cloud' => '☁️ Cloud',
    'fa-snowflake' => '❄️ Snowflake',
    'fa-trophy' => '🏆 Trophy',
    'fa-medal' => '🥇 Medal',
    'fa-gift' => '🎁 Gift',
];

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="brand-font" style="color: var(--primary-blue);">
        <?php echo $is_edit ? 'Edit Sightseeing Destination' : 'Add New Sightseeing Destination'; ?>
    </h2>
    <div>
        <a href="sightseeing_edit.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
        <?php if($is_edit): ?>
        <a href="sightseeing_edit.php" class="btn btn-primary-green">
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
                <h5 class="mb-0"><i class="bi <?php echo $is_edit ? 'bi-pencil-square' : 'bi-plus-circle'; ?> me-2"></i> <?php echo $is_edit ? 'Edit Details' : 'Add New Destination'; ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" id="sightseeingForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($sightseeing['title'] ?? ''); ?>" id="titleInput" placeholder="e.g., 7 Points" required>
                            <small class="text-muted">e.g., "7 Points", "3 Points", "Extra & Out of Town"</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Badge Label</label>
                            <input type="text" name="badge" class="form-control" value="<?php echo htmlspecialchars($sightseeing['badge'] ?? 'Must Visit'); ?>" id="badgeInput" placeholder="e.g., Must Visit">
                            <small class="text-muted">e.g., "Must Visit", "Scenic", "Explore"</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Icon <span class="text-danger">*</span></label>
                            <select name="icon" class="form-select" id="iconSelect" style="font-size: 1rem;">
                                <?php foreach($available_icons as $icon_class => $icon_label): ?>
                                <option value="<?php echo $icon_class; ?>" <?php echo ($sightseeing['icon'] ?? 'fa-mountain') == $icon_class ? 'selected' : ''; ?>>
                                    <?php echo $icon_label; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Select an icon that represents this destination</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Color (Hex Code)</label>
                            <div class="input-group">
                                <input type="color" name="color" class="form-control form-control-color" value="<?php echo htmlspecialchars($sightseeing['color'] ?? '#1f6332'); ?>" id="colorInput" style="width: 60px; padding: 0.25rem; border-radius: 8px 0 0 8px;">
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($sightseeing['color'] ?? '#1f6332'); ?>" id="colorText" readonly style="border-radius: 0 8px 8px 0; background: #f8f9fa; font-family: monospace;">
                            </div>
                            <small class="text-muted">Choose a color for the card header</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Points / Places <span class="text-danger">*</span></label>
                        <textarea name="points" class="form-control" rows="6" id="pointsInput" placeholder="Enter each point to get a new line..." required><?php echo htmlspecialchars($sightseeing['points'] ?? ''); ?></textarea>
                        <small class="text-muted">Press <span style="font-weight: bold;">Enter</span> each point to get a new line. Example:<br>
                        <code>Zoo &amp; HMI</code><br>
                        <code>Museum &amp; Tea Garden</code><br>
                        <code>Tenzing Rock &amp; Japanese Temple</code></small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="<?php echo $sightseeing['sort_order'] ?? 0; ?>" id="sortOrderInput" placeholder="0">
                            <small class="text-muted">Lower numbers appear first on the website</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <div class="form-check mt-2">
                                <input type="checkbox" name="active" class="form-check-input" id="activeInput" <?php echo ($sightseeing['active'] ?? 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label">Active (visible on website)</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap mt-3">
                        <button type="submit" class="btn btn-primary-green">
                            <i class="bi bi-save"></i> <?php echo $is_edit ? 'Update' : 'Add'; ?>
                        </button>
                        
                        <?php if($is_edit): ?>
                        <a href="sightseeing_edit.php" class="btn btn-secondary">
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

<!-- Existing Sightseeing Table -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background: #6c757d; color: white; display: flex; justify-content: space-between; align-items: center;">
                <h5 class="mb-0"><i class="bi bi-list me-2"></i> All Sightseeing Destinations</h5>
                <span class="badge bg-light text-dark">Total: <?php echo count($all_sightseeing); ?></span>
            </div>
            <div class="card-body">
                <?php if(empty($all_sightseeing)): ?>
                <div class="text-center py-4">
                    <i class="bi bi-map fs-1 text-muted d-block mb-3"></i>
                    <p class="text-muted">No sightseeing destinations found. Add your first destination above!</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Badge</th>
                                <th>Icon</th>
                                <th>Points</th>
                                <th>Sort</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($all_sightseeing as $item): ?>
                            <tr <?php echo ($is_edit && $sightseeing && $item['id'] == $sightseeing['id']) ? 'class="table-primary"' : ''; ?>>
                                <td><?php echo $item['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                    <br><small class="text-muted">
                                        <span style="display:inline-block; width:16px; height:16px; background:<?php echo $item['color']; ?>; border-radius:4px; vertical-align:middle; border:1px solid #ddd;"></span>
                                        <?php echo $item['color']; ?>
                                    </small>
                                </td>
                                <td><span class="badge bg-primary"><?php echo htmlspecialchars($item['badge']); ?></span></td>
                                <td>
                                    <span style="font-size: 1.4rem;">
                                        <i class="fas <?php echo $item['icon']; ?>"></i>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $points = explode("\n", trim($item['points']));
                                    $count = count(array_filter($points, 'trim'));
                                    echo $count . ' point' . ($count > 1 ? 's' : '');
                                    ?>
                                </td>
                                <td><?php echo $item['sort_order']; ?></td>
                                <td>
                                    <span class="badge <?php echo $item['active'] ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $item['active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?id=<?php echo $item['id']; ?>" class="btn btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="?delete=<?php echo $item['id']; ?>" class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this destination?')">
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

<!-- Preview Template (hidden) -->
<template id="previewTemplate">
    <div class="sightseeing-preview">
        <div class="preview-header" id="previewHeader" style="background: linear-gradient(135deg, #1f6332, #2e7d32); color: #fff; padding: 16px 20px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center; font-weight: 600;">
            <span><i class="fas fa-mountain" id="previewIcon"></i> <span id="previewTitle">Title</span></span>
            <span class="preview-badge" id="previewBadge" style="background: rgba(255,255,255,0.2); padding: 4px 14px; border-radius: 50px; font-size: 0.75rem;">Badge</span>
        </div>
        <div class="preview-body" style="background: #fff; border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 12px 12px;">
            <div id="previewPoints">
                <!-- Points will be inserted here -->
            </div>
        </div>
    </div>
</template>

<style>
    .sightseeing-preview {
        border: 1px solid #dee2e6;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    }
    
    .sightseeing-preview .preview-body {
        padding: 0;
    }
    
    .sightseeing-preview .preview-body .point-item {
        padding: 12px 20px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: background 0.2s ease;
    }
    
    .sightseeing-preview .preview-body .point-item:last-child {
        border-bottom: none;
    }
    
    .sightseeing-preview .preview-body .point-item:hover {
        background: #f8f9fa;
    }
    
    .sightseeing-preview .preview-body .point-icon {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #e8f5e9;
        color: #2e7d32;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
        flex-shrink: 0;
    }
    
    .sightseeing-preview .preview-body .point-name {
        font-weight: 500;
        color: #333;
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
    
    .form-control-color {
        padding: 0.25rem;
        height: 38px;
        cursor: pointer;
    }
    
    .form-select {
        border-radius: 8px;
        padding: 10px 14px;
        border-color: #e0e0e0;
        transition: 0.3s;
        font-size: 0.95rem;
    }
    
    .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(31, 99, 50, 0.15);
    }
    
    .form-select option {
        padding: 8px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const previewContainer = document.getElementById('previewContainer');
    const template = document.getElementById('previewTemplate');
    
    // Get all input elements
    const titleInput = document.getElementById('titleInput');
    const badgeInput = document.getElementById('badgeInput');
    const iconSelect = document.getElementById('iconSelect');
    const colorInput = document.getElementById('colorInput');
    const colorText = document.getElementById('colorText');
    const pointsInput = document.getElementById('pointsInput');
    
    // Update color text when color picker changes
    if (colorInput && colorText) {
        colorInput.addEventListener('input', function() {
            colorText.value = this.value;
            updatePreview();
        });
    }
    
    // Function to update preview
    function updatePreview() {
        // Clone the template content
        const previewContent = template.content.cloneNode(true);
        
        // Get preview elements
        const previewHeader = previewContent.getElementById('previewHeader');
        const previewIcon = previewContent.getElementById('previewIcon');
        const previewTitle = previewContent.getElementById('previewTitle');
        const previewBadge = previewContent.getElementById('previewBadge');
        const previewPoints = previewContent.getElementById('previewPoints');
        
        // Update header color
        const color = colorInput.value || '#1f6332';
        previewHeader.style.background = `linear-gradient(135deg, ${color}, ${color}cc)`;
        
        // Update icon
        const iconClass = iconSelect.value || 'fa-mountain';
        previewIcon.className = 'fas ' + iconClass;
        
        // Update title
        const title = titleInput.value.trim() || 'Title';
        previewTitle.textContent = title;
        
        // Update badge
        const badge = badgeInput.value.trim() || 'Badge';
        previewBadge.textContent = badge;
        
        // Update points
        const pointsText = pointsInput.value || '';
        const pointsArray = pointsText.split('\n').filter(point => point.trim() !== '');
        
        if (pointsArray.length === 0) {
            previewPoints.innerHTML = `
                <div class="point-item">
                    <span class="text-muted" style="padding: 12px 20px; display: block;">No points added yet</span>
                </div>
            `;
        } else {
            let pointsHTML = '';
            let pointNumber = 1;
            pointsArray.forEach(function(point) {
                pointsHTML += `
                    <div class="point-item">
                        <span class="point-icon">${pointNumber}</span>
                        <span class="point-name">${escapeHtml(point.trim())}</span>
                    </div>
                `;
                pointNumber++;
            });
            previewPoints.innerHTML = pointsHTML;
        }
        
        // Update preview container
        previewContainer.innerHTML = '';
        previewContainer.appendChild(previewContent);
    }
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Add event listeners to all inputs
    const inputs = [titleInput, badgeInput, iconSelect, colorInput, pointsInput];
    inputs.forEach(input => {
        if (input) {
            input.addEventListener('input', updatePreview);
            input.addEventListener('change', updatePreview);
        }
    });
    
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