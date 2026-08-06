<?php
// admin/about.php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
require_once '../include/functions.php';

$error = '';
$success = '';

// Get current about page content
$page = getPage($pdo, 'about');

// Get about settings from the new about_settings table
$stmt = $pdo->query("SELECT setting_key, setting_value FROM about_settings");
$about_settings = [];
while($row = $stmt->fetch()) {
    $about_settings[$row['setting_key']] = $row['setting_value'];
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = $_POST['content'];
    $meta_description = trim($_POST['meta_description']);
    $meta_keywords = trim($_POST['meta_keywords']);
    
    // About settings
    $mission = trim($_POST['mission']);
    $vision = trim($_POST['vision']);
    $values = trim($_POST['values']);
    $story_title = trim($_POST['story_title']);
    $story_content = $_POST['story_content'];
    $team_title = trim($_POST['team_title']);
    $team_description = trim($_POST['team_description']);
    $why_choose_us = trim($_POST['why_choose_us']);
    $stats_customers = trim($_POST['stats_customers']);
    $stats_packages = trim($_POST['stats_packages']);
    $stats_destinations = trim($_POST['stats_destinations']);
    $stats_experience = trim($_POST['stats_experience']);

    try {
        // Update page content
        $stmt = $pdo->prepare("UPDATE pages SET 
            title = ?, content = ?, meta_description = ?, meta_keywords = ? 
            WHERE page_name = 'about'");
        $stmt->execute([$title, $content, $meta_description, $meta_keywords]);
        
        // Update about settings
        $settings_data = [
            'mission' => $mission,
            'vision' => $vision,
            'values' => $values,
            'story_title' => $story_title,
            'story_content' => $story_content,
            'team_title' => $team_title,
            'team_description' => $team_description,
            'why_choose_us' => $why_choose_us,
            'stats_customers' => $stats_customers,
            'stats_packages' => $stats_packages,
            'stats_destinations' => $stats_destinations,
            'stats_experience' => $stats_experience
        ];
        
        foreach($settings_data as $key => $value) {
            $stmt = $pdo->prepare("UPDATE about_settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $key]);
        }
        
        // Use PRG pattern
        $_SESSION['success_message'] = 'About page updated successfully!';
        header('Location: about.php');
        exit;
    } catch(PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Check for success message from session (PRG pattern)
if(isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
    
    // Refresh data
    $page = getPage($pdo, 'about');
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM about_settings");
    $about_settings = [];
    while($row = $stmt->fetch()) {
        $about_settings[$row['setting_key']] = $row['setting_value'];
    }
}

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="brand-font" style="color: var(--primary-blue);">
        <i class="bi bi-info-circle me-2"></i> About Page Management
    </h2>
    <div class="d-flex gap-2 flex-wrap">
        <a href="../about.php" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-eye"></i> View Page
        </a>
        <a href="pages.php" class="btn btn-info text-white">
            <i class="bi bi-arrow-left"></i> Back to Pages
        </a>
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
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-blue), var(--primary-green)); color: white;">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Page Content</h5>
            </div>
            <div class="card-body">
                <form method="POST" id="aboutForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Page Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($page['title'] ?? 'About Us'); ?>" id="titleInput" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Content <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="8" id="contentEditor"><?php echo htmlspecialchars($page['content'] ?? ''); ?></textarea>
                        <small class="text-muted">Use HTML tags: &lt;h2&gt;, &lt;h3&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;, &lt;em&gt;</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Meta Description</label>
                            <input type="text" name="meta_description" class="form-control" value="<?php echo htmlspecialchars($page['meta_description'] ?? ''); ?>" placeholder="SEO description" maxlength="160">
                            <small class="text-muted">Max 160 characters. Recommended for SEO.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Meta Keywords</label>
                            <input type="text" name="meta_keywords" class="form-control" value="<?php echo htmlspecialchars($page['meta_keywords'] ?? ''); ?>" placeholder="keyword1, keyword2, keyword3">
                            <small class="text-muted">Comma-separated keywords</small>
                        </div>
                    </div>
                    
                    <hr>
                    <h5 class="mt-3" style="color: var(--primary-blue);">
                        <i class="bi bi-bullseye me-2" style="color: var(--primary-green);"></i> About Section Settings
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Mission Statement</label>
                            <textarea name="mission" class="form-control" rows="2" placeholder="Our mission is..."><?php echo htmlspecialchars($about_settings['mission'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Vision Statement</label>
                            <textarea name="vision" class="form-control" rows="2" placeholder="Our vision is..."><?php echo htmlspecialchars($about_settings['vision'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Core Values</label>
                        <input type="text" name="values" class="form-control" value="<?php echo htmlspecialchars($about_settings['values'] ?? ''); ?>" placeholder="e.g., Integrity, Customer First, Quality Service">
                        <small class="text-muted">Comma-separated values</small>
                    </div>
                    
                    <hr>
                    <h5 class="mt-3" style="color: var(--primary-blue);">
                        <i class="bi bi-book me-2" style="color: var(--primary-green);"></i> Story Section
                    </h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Story Title</label>
                        <input type="text" name="story_title" class="form-control" value="<?php echo htmlspecialchars($about_settings['story_title'] ?? 'Our Story'); ?>" placeholder="Our Story">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Story Content</label>
                        <textarea name="story_content" class="form-control" rows="4" placeholder="Tell your story..."><?php echo htmlspecialchars($about_settings['story_content'] ?? ''); ?></textarea>
                    </div>
                    
                    <hr>
                    <h5 class="mt-3" style="color: var(--primary-blue);">
                        <i class="bi bi-people me-2" style="color: var(--primary-green);"></i> Team Section
                    </h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Team Title</label>
                        <input type="text" name="team_title" class="form-control" value="<?php echo htmlspecialchars($about_settings['team_title'] ?? 'Meet Our Team'); ?>" placeholder="Meet Our Team">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Team Description</label>
                        <textarea name="team_description" class="form-control" rows="2" placeholder="Describe your team..."><?php echo htmlspecialchars($about_settings['team_description'] ?? ''); ?></textarea>
                    </div>
                    
                    <hr>
                    <h5 class="mt-3" style="color: var(--primary-blue);">
                        <i class="bi bi-check-circle me-2" style="color: var(--primary-green);"></i> Why Choose Us
                    </h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Why Choose Us (comma separated)</label>
                        <textarea name="why_choose_us" class="form-control" rows="3" placeholder="Local expertise, Customized itineraries, Premium fleet..."><?php echo htmlspecialchars($about_settings['why_choose_us'] ?? ''); ?></textarea>
                        <small class="text-muted">Separate each point with a comma</small>
                    </div>
                    
                    <hr>
                    <h5 class="mt-3" style="color: var(--primary-blue);">
                        <i class="bi bi-graph-up me-2" style="color: var(--primary-green);"></i> Statistics
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Happy Customers</label>
                            <input type="text" name="stats_customers" class="form-control" value="<?php echo htmlspecialchars($about_settings['stats_customers'] ?? '5000+'); ?>" placeholder="5000+">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Tour Packages</label>
                            <input type="text" name="stats_packages" class="form-control" value="<?php echo htmlspecialchars($about_settings['stats_packages'] ?? '100+'); ?>" placeholder="100+">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Destinations</label>
                            <input type="text" name="stats_destinations" class="form-control" value="<?php echo htmlspecialchars($about_settings['stats_destinations'] ?? '50+'); ?>" placeholder="50+">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Years Experience</label>
                            <input type="text" name="stats_experience" class="form-control" value="<?php echo htmlspecialchars($about_settings['stats_experience'] ?? '10+'); ?>" placeholder="10+">
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 flex-wrap mt-3">
                        <button type="submit" class="btn btn-primary-green">
                            <i class="bi bi-save"></i> Update About Page
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Live Preview Section -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-blue), var(--primary-green)); color: white;">
                <h5 class="mb-0"><i class="bi bi-eye me-2"></i> Live Preview</h5>
            </div>
            <div class="card-body">
                <div id="previewContainer" class="preview-content">
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-pencil-square fs-1 d-block mb-3"></i>
                        <p>Start editing to see preview</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Tips -->
        <div class="card mt-3">
            <div class="card-header" style="background: var(--primary-blue); color: white;">
                <h5 class="mb-0"><i class="bi bi-lightbulb me-2"></i> Tips</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Use &lt;h2&gt; for main headings</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Use &lt;h3&gt; for sub-headings</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Use &lt;ul&gt; and &lt;li&gt; for lists</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Keep content engaging and informative</li>
                    <li class="mb-0"><i class="bi bi-check-circle text-success me-2"></i> Include keywords naturally for SEO</li>
                </ul>
            </div>
        </div>
        
        <!-- Content Stats -->
        <div class="card mt-3">
            <div class="card-header" style="background: #6c757d; color: white;">
                <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i> Content Stats</h5>
            </div>
            <div class="card-body">
                <div id="contentStats">
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>Characters:</span>
                        <strong id="charCount">0</strong>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>Words:</span>
                        <strong id="wordCount">0</strong>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>Headings:</span>
                        <strong id="headingCount">0</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span>Paragraphs:</span>
                        <strong id="paragraphCount">0</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .preview-content {
        min-height: 250px;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        border: 2px dashed #dee2e6;
        max-height: 500px;
        overflow-y: auto;
    }
    
    .preview-content .preview-wrapper {
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .preview-content .preview-title {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        color: var(--primary-blue);
        border-bottom: 3px solid var(--primary-green);
        padding-bottom: 10px;
        margin-bottom: 15px;
    }
    
    .preview-content h2 {
        font-size: 1.5rem;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        color: var(--primary-blue);
        font-weight: 700;
    }
    
    .preview-content h3 {
        font-size: 1.2rem;
        margin-top: 0.75rem;
        margin-bottom: 0.5rem;
        color: var(--primary-blue);
        font-weight: 600;
    }
    
    .preview-content p {
        line-height: 1.8;
        margin-bottom: 0.75rem;
        color: #2d3436;
    }
    
    .preview-content ul {
        padding-left: 20px;
        margin-bottom: 1rem;
    }
    
    .preview-content ul li {
        margin-bottom: 0.5rem;
        line-height: 1.6;
        list-style: none;
        padding-left: 20px;
        position: relative;
    }
    
    .preview-content ul li:before {
        content: "✦";
        color: var(--primary-green);
        position: absolute;
        left: 0;
        top: 0;
        font-weight: bold;
    }
    
    .preview-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 10px 0;
    }
    
    .preview-content blockquote {
        border-left: 4px solid var(--primary-green);
        padding: 10px 20px;
        margin: 15px 0;
        background: #f8f9fa;
        border-radius: 0 8px 8px 0;
        font-style: italic;
    }
    
    .preview-content .text-muted {
        text-align: center;
        padding: 40px 0;
    }
    
    .form-control {
        border-radius: 8px;
        padding: 10px 14px;
        border-color: #e0e0e0;
        transition: 0.3s;
    }
    
    .form-control:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(31, 99, 50, 0.15);
    }
    
    textarea.form-control {
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        resize: vertical;
        min-height: 60px;
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
    
    .btn-primary-green {
        background: var(--primary-green);
        color: white;
        border: none;
        transition: 0.3s;
        border-radius: 8px;
        padding: 10px 25px;
        font-weight: 600;
    }
    
    .btn-primary-green:hover {
        background: #155724;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(31, 99, 50, 0.3);
    }
    
    .btn-info {
        background: var(--primary-blue);
        border: none;
        transition: 0.3s;
    }
    
    .btn-info:hover {
        background: #012a52;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(1, 53, 101, 0.3);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentEditor = document.getElementById('contentEditor');
    const previewContainer = document.getElementById('previewContainer');
    const titleInput = document.getElementById('titleInput');
    const charCount = document.getElementById('charCount');
    const wordCount = document.getElementById('wordCount');
    const headingCount = document.getElementById('headingCount');
    const paragraphCount = document.getElementById('paragraphCount');
    
    // Auto-resize textarea
    function autoResize() {
        contentEditor.style.height = 'auto';
        contentEditor.style.height = contentEditor.scrollHeight + 'px';
    }
    autoResize();
    
    // Update content stats
    function updateStats(content) {
        // Character count
        charCount.textContent = content.length;
        
        // Word count
        const words = content.trim() ? content.trim().split(/\s+/).length : 0;
        wordCount.textContent = words;
        
        // Heading count (h2, h3)
        const h2Count = (content.match(/<h2/g) || []).length;
        const h3Count = (content.match(/<h3/g) || []).length;
        headingCount.textContent = h2Count + h3Count;
        
        // Paragraph count
        const pCount = (content.match(/<p/g) || []).length;
        paragraphCount.textContent = pCount;
    }
    
    // Update preview function
    function updatePreview() {
        const content = contentEditor.value;
        const title = titleInput.value.trim() || 'About Us';
        
        if (content.trim()) {
            // Create a preview wrapper with title
            const previewHtml = `
                <div class="preview-wrapper">
                    <h2 class="preview-title">${escapeHtml(title)}</h2>
                    ${content}
                </div>
            `;
            previewContainer.innerHTML = previewHtml;
        } else {
            previewContainer.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="bi bi-pencil-square fs-1 d-block mb-3"></i>
                    <p>Start editing to see preview</p>
                </div>
            `;
        }
        
        // Update stats
        updateStats(content);
    }
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Event listeners
    contentEditor.addEventListener('input', function() {
        autoResize();
        updatePreview();
    });
    
    titleInput.addEventListener('input', updatePreview);
    
    // Initial preview update
    updatePreview();
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            const closeButton = alert.querySelector('.btn-close');
            if (closeButton) {
                setTimeout(function() {
                    closeButton.click();
                }, 5000);
            }
        });
    }, 1000);
});
</script>

<?php include 'footer.php'; ?>