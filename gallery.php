<?php
// gallery.php
require_once 'config/database.php';
require_once 'include/functions.php';

$settings = getSettings($pdo);
$page = getPage($pdo, 'gallery');
$gallery_images = getGallery($pdo);

include 'include/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $settings['site_name'] ?? 'CrossWay Travel'; ?> - Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* ============================================
           GALLERY PAGE STYLES
           ============================================ */
        
        /* Hero Section */
        .gallery-hero {
            background: linear-gradient(135deg, #013565 0%, #1f6332 100%);
            color: white;
            padding: 60px 0;
            border-radius: 15px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }
        .gallery-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .gallery-hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .gallery-hero h1 {
            font-size: 3rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        .gallery-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        .divider {
            width: 80px;
            height: 4px;
            background: #f5b342;
            margin: 20px auto;
            border-radius: 2px;
        }

        /* Filter Section */
        .filter-section {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            border: 1px solid #f0f0f0;
        }

        .filter-section .form-control,
        .filter-section .form-select {
            border-radius: 50px;
            padding: 10px 20px;
            border-color: #e0e0e0;
        }

        .filter-section .form-control:focus,
        .filter-section .form-select:focus {
            border-color: #1f6332;
            box-shadow: 0 0 0 0.2rem rgba(31, 99, 50, 0.1);
        }

        .filter-section .btn-filter {
            background: linear-gradient(135deg, #1f6332, #2e7d32);
            color: #fff;
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
        }

        .filter-section .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(31, 99, 50, 0.3);
            color: #fff;
        }

        /* Gallery Grid - Uniform Size */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .gallery-item-modern {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            aspect-ratio: 1 / 1;
            transition: all 0.4s ease;
            background: #f8f9fa;
        }

        .gallery-item-modern img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .gallery-item-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        }

        .gallery-item-modern:hover img {
            transform: scale(1.08);
        }

        .gallery-item-modern .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(transparent, rgba(1, 53, 101, 0.85));
            opacity: 0;
            transition: all 0.4s ease;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 20px;
            color: #fff;
        }

        .gallery-item-modern:hover .overlay {
            opacity: 1;
        }

        .gallery-item-modern .overlay .category-badge {
            display: inline-block;
            background: rgba(249, 168, 37, 0.9);
            color: #1a1a2e;
            padding: 3px 14px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-bottom: 8px;
            width: fit-content;
            transform: translateY(10px);
            transition: transform 0.4s ease 0.1s;
        }

        .gallery-item-modern:hover .overlay .category-badge {
            transform: translateY(0);
        }

        .gallery-item-modern .overlay h6 {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 3px;
            transform: translateY(10px);
            transition: transform 0.4s ease;
        }

        .gallery-item-modern:hover .overlay h6 {
            transform: translateY(0);
        }

        .gallery-item-modern .overlay .description {
            font-size: 0.8rem;
            opacity: 0.9;
            margin-bottom: 0;
            transform: translateY(10px);
            transition: transform 0.4s ease 0.2s;
        }

        .gallery-item-modern:hover .overlay .description {
            transform: translateY(0);
        }

        .gallery-item-modern .overlay .icon-expand {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: all 0.4s ease;
            transform: scale(0);
        }

        .gallery-item-modern:hover .overlay .icon-expand {
            transform: scale(1);
            background: rgba(249, 168, 37, 0.8);
            color: #1a1a2e;
        }

        .gallery-item-modern .overlay .icon-expand:hover {
            transform: scale(1.1) rotate(90deg);
        }

        /* Lightbox */
        .lightbox-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.92);
            backdrop-filter: blur(20px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 40px;
            animation: lightboxFadeIn 0.5s ease;
        }

        .lightbox-overlay.active {
            display: flex;
        }

        @keyframes lightboxFadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .lightbox-overlay .lightbox-close {
            position: absolute;
            top: 30px;
            right: 40px;
            color: #fff;
            font-size: 2.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lightbox-overlay .lightbox-close:hover {
            transform: rotate(90deg);
            background: rgba(249, 168, 37, 0.3);
            border-color: #f9a825;
        }

        .lightbox-overlay .lightbox-content {
            max-width: 90vw;
            max-height: 85vh;
            position: relative;
            animation: lightboxZoomIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes lightboxZoomIn {
            from {
                opacity: 0;
                transform: scale(0.5) rotate(-5deg);
            }
            to {
                opacity: 1;
                transform: scale(1) rotate(0deg);
            }
        }

        .lightbox-overlay .lightbox-content img {
            max-width: 100%;
            max-height: 85vh;
            border-radius: 20px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.6);
            border: 2px solid rgba(255,255,255,0.1);
        }

        .lightbox-overlay .lightbox-content .lightbox-caption {
            position: absolute;
            bottom: -60px;
            left: 0;
            right: 0;
            text-align: center;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 500;
            animation: lightboxSlideUp 0.6s ease 0.3s both;
        }

        @keyframes lightboxSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .lightbox-overlay .lightbox-content .lightbox-caption small {
            opacity: 0.6;
            font-weight: 300;
        }

        .lightbox-overlay .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #fff;
            font-size: 2.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5;
        }

        .lightbox-overlay .lightbox-nav:hover {
            background: rgba(249, 168, 37, 0.3);
            border-color: #f9a825;
            transform: translateY(-50%) scale(1.1);
        }

        .lightbox-overlay .lightbox-nav.prev {
            left: 40px;
        }

        .lightbox-overlay .lightbox-nav.next {
            right: 40px;
        }

        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 40px 0;
            border-radius: 12px;
            margin: 30px 0;
        }
        .stat-item {
            text-align: center;
            padding: 20px;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #013565;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .stat-icon {
            font-size: 2rem;
            color: #1f6332;
            margin-bottom: 10px;
            display: block;
        }

        /* No Results */
        #noResults {
            display: none;
        }
        #noResults i {
            color: #6c757d;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .gallery-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 15px;
            }
        }

        @media (max-width: 768px) {
            .gallery-hero h1 {
                font-size: 2rem;
            }
            .gallery-hero p {
                font-size: 1rem;
            }
            .stat-number {
                font-size: 1.8rem;
            }
            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            .lightbox-overlay .lightbox-nav {
                display: none;
            }
            .filter-section .btn-filter {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            .gallery-item-modern .overlay {
                padding: 12px;
            }
            .gallery-item-modern .overlay h6 {
                font-size: 0.8rem;
            }
            .gallery-item-modern .overlay .description {
                font-size: 0.7rem;
            }
            .gallery-item-modern .overlay .category-badge {
                font-size: 0.6rem;
                padding: 2px 10px;
            }
            .gallery-item-modern .overlay .icon-expand {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
                top: 10px;
                right: 10px;
            }
            .lightbox-overlay {
                padding: 15px;
            }
            .lightbox-overlay .lightbox-close {
                top: 15px;
                right: 15px;
                width: 40px;
                height: 40px;
                font-size: 1.5rem;
            }
        }

        @media (max-width: 400px) {
            .gallery-grid {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }
        }
    </style>
</head>
<body>

<!-- Hero Section -->
<section class="gallery-hero">
    <div class="container text-center">
        <h1><i class="fas fa-images me-2"></i> Photo Gallery</h1>
        <div class="divider"></div>
        <p>Explore our collection of travel moments captured across the Himalayas</p>
    </div>
</section>

<div class="container py-4">
    <!-- Filter/Search Section -->
    <div class="filter-section">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-bold"><i class="fas fa-search me-1"></i> Search Gallery</label>
                <input type="text" class="form-control" id="searchGallery" placeholder="Search by title or category..." onkeyup="filterGallery()">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold"><i class="fas fa-filter me-1"></i> Filter by Category</label>
                <select class="form-select" id="filterCategory" onchange="filterGallery()">
                    <option value="all">All Categories</option>
                    <?php 
                    $categories = array_unique(array_column($gallery_images, 'category'));
                    foreach($categories as $category): 
                        if(!empty($category)):
                    ?>
                    <option value="<?php echo strtolower($category); ?>"><?php echo $category; ?></option>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn-filter w-100" onclick="resetFilters()">
                    <i class="fas fa-undo me-1"></i> Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Gallery Grid - All Images Same Size -->
    <div class="gallery-grid" id="galleryGrid">
        <?php foreach($gallery_images as $index => $image): ?>
        <div class="gallery-item-modern" 
             data-index="<?php echo $index; ?>" 
             data-title="<?php echo strtolower($image['title']); ?>" 
             data-category="<?php echo strtolower($image['category'] ?? ''); ?>"
             onclick="openLightbox(<?php echo $index; ?>)">
            <img src="<?php echo $image['image_path']; ?>" alt="<?php echo $image['title']; ?>">
            <div class="overlay">
                <span class="category-badge"><?php echo $image['category'] ?? 'Himalayan Beauty'; ?></span>
                <h6><?php echo $image['title']; ?></h6>
                <?php if($image['description']): ?>
                <p class="description"><?php echo truncateText($image['description'], 50); ?></p>
                <?php endif; ?>
                <div class="icon-expand">
                    <i class="fas fa-expand"></i>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- No Results Message -->
    <div id="noResults" class="text-center py-5">
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h4>No images found</h4>
        <p class="text-muted">Try adjusting your search or filter criteria</p>
        <button class="btn btn-primary" onclick="resetFilters()" style="border-radius: 50px; padding: 10px 30px; background: linear-gradient(135deg, #1f6332, #2e7d32); border: none;">
            <i class="fas fa-undo me-1"></i> Reset Filters
        </button>
    </div>

 
    <!-- Call to Action -->
    <div class="text-center mt-5">
        <h3 style="color: #013565; font-weight: 700;">Want to Share Your Travel Moments?</h3>
        <p class="text-muted">Send us your photos and we'll feature them in our gallery</p>
        <a href="contact.php" class="btn btn-primary" style="border-radius: 50px; padding: 14px 45px; background: linear-gradient(135deg, #013565, #1f6332); border: none; box-shadow: 0 8px 30px rgba(1, 53, 101, 0.3); transition: all 0.3s ease;">
            <i class="fas fa-upload me-2"></i> Share Your Photos
        </a>
    </div>
</div>

<!-- Lightbox Overlay -->
<div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightbox(event)">
    <div class="lightbox-close" onclick="closeLightbox(event)">
        <i class="fas fa-times"></i>
    </div>
    <div class="lightbox-nav prev" onclick="navigateLightbox(-1, event)">
        <i class="fas fa-chevron-left"></i>
    </div>
    <div class="lightbox-nav next" onclick="navigateLightbox(1, event)">
        <i class="fas fa-chevron-right"></i>
    </div>
    <div class="lightbox-content" onclick="event.stopPropagation();">
        <img id="lightboxImage" src="" alt="">
        <div class="lightbox-caption" id="lightboxCaption">
            <span id="lightboxTitle"></span> 
            <small id="lightboxCategory"></small>
            <br>
            <small id="lightboxDescription" style="opacity: 0.7; font-weight: 300;"></small>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ============================================
    // GALLERY DATA
    // ============================================
    <?php 
    $gallery_json = json_encode($gallery_images);
    ?>
    const galleryData = <?php echo $gallery_json; ?>;
    let currentLightboxIndex = 0;

    // ============================================
    // LIGHTBOX FUNCTIONS
    // ============================================
    function openLightbox(index) {
        currentLightboxIndex = index;
        const overlay = document.getElementById('lightboxOverlay');
        const img = document.getElementById('lightboxImage');
        const title = document.getElementById('lightboxTitle');
        const category = document.getElementById('lightboxCategory');
        const description = document.getElementById('lightboxDescription');
        
        const item = galleryData[index];
        img.src = item.image_path;
        title.textContent = item.title;
        category.textContent = item.category || 'Himalayan Beauty';
        description.textContent = item.description || '';
        
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox(event) {
        if (event && event.target !== event.currentTarget && !event.target.closest('.lightbox-close')) {
            return;
        }
        const overlay = document.getElementById('lightboxOverlay');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function navigateLightbox(direction, event) {
        if (event) {
            event.stopPropagation();
        }
        const total = galleryData.length;
        currentLightboxIndex = (currentLightboxIndex + direction + total) % total;
        
        const img = document.getElementById('lightboxImage');
        const title = document.getElementById('lightboxTitle');
        const category = document.getElementById('lightboxCategory');
        const description = document.getElementById('lightboxDescription');
        
        const item = galleryData[currentLightboxIndex];
        
        const content = document.querySelector('.lightbox-content');
        content.style.animation = 'none';
        setTimeout(() => {
            content.style.animation = 'lightboxZoomIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
        }, 10);
        
        img.src = item.image_path;
        title.textContent = item.title;
        category.textContent = item.category || 'Himalayan Beauty';
        description.textContent = item.description || '';
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        const overlay = document.getElementById('lightboxOverlay');
        if (!overlay.classList.contains('active')) return;
        
        if (e.key === 'Escape') {
            closeLightbox(e);
        } else if (e.key === 'ArrowLeft') {
            navigateLightbox(-1, e);
        } else if (e.key === 'ArrowRight') {
            navigateLightbox(1, e);
        }
    });

    // ============================================
    // FILTER FUNCTIONS
    // ============================================
    function filterGallery() {
        const searchInput = document.getElementById('searchGallery');
        const categoryFilter = document.getElementById('filterCategory');
        const searchTerm = searchInput.value.toLowerCase().trim();
        const category = categoryFilter.value.toLowerCase();
        
        const items = document.querySelectorAll('.gallery-item-modern');
        let visibleCount = 0;
        
        items.forEach(function(item) {
            const title = item.dataset.title || '';
            const itemCategory = item.dataset.category || '';
            
            const matchesSearch = title.includes(searchTerm) || itemCategory.includes(searchTerm);
            const matchesCategory = category === 'all' || itemCategory === category;
            
            if (matchesSearch && matchesCategory) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        const noResults = document.getElementById('noResults');
        const grid = document.getElementById('galleryGrid');
        if (visibleCount === 0) {
            noResults.style.display = 'block';
            grid.style.display = 'none';
        } else {
            noResults.style.display = 'none';
            grid.style.display = 'grid';
        }
    }
    
    function resetFilters() {
        document.getElementById('searchGallery').value = '';
        document.getElementById('filterCategory').value = 'all';
        filterGallery();
    }
    
    // Search on Enter key
    document.getElementById('searchGallery').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            filterGallery();
        }
    });
</script>
</body>
</html>