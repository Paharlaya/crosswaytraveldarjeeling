<?php
// admin/pages.php
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
    $page_name = $_POST['page_name'];
    $title = trim($_POST['title']);
    $content = $_POST['content'];
    $meta_description = trim($_POST['meta_description']);
    $meta_keywords = trim($_POST['meta_keywords']);

    try {
        $stmt = $pdo->prepare("UPDATE pages SET 
            title = ?, content = ?, meta_description = ?, meta_keywords = ? 
            WHERE page_name = ?");
        $stmt->execute([$title, $content, $meta_description, $meta_keywords, $page_name]);
        $success = 'Page updated successfully!';
    } catch(PDOException $e) {
        $error = 'An error occurred.';
    }
}

$pages = $pdo->query("SELECT * FROM pages ORDER BY page_name")->fetchAll();

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Manage Pages</h2>
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

<div class="row">
    <?php foreach($pages as $page): ?>
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5><?php echo ucfirst($page['page_name']); ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="page_name" value="<?php echo $page['page_name']; ?>">
                    <div class="mb-2">
                        <label class="form-label small">Title</label>
                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($page['title']); ?>" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Content</label>
                        <textarea name="content" class="form-control" rows="4"><?php echo htmlspecialchars($page['content']); ?></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Meta Description</label>
                        <input type="text" name="meta_description" class="form-control" value="<?php echo htmlspecialchars($page['meta_description'] ?? ''); ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control" value="<?php echo htmlspecialchars($page['meta_keywords'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save"></i> Update
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php include 'footer.php'; ?>