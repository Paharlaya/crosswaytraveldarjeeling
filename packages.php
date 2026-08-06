<?php
// packages.php
require_once 'config/database.php';
require_once 'include/functions.php';

$settings = getSettings($pdo);
$page = getPage($pdo, 'packages');
$packages = getPackages($pdo);

include 'include/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $settings['site_name'] ?? 'CrossWay Travel'; ?> - Packages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* ============================================
           PACKAGES PAGE STYLES
           ============================================ */
        
        /* Hero Section */
        .packages-hero {
            background: linear-gradient(135deg, #013565 0%, #1f6332 100%);
            color: white;
            padding: 60px 0;
            border-radius: 15px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }
        .packages-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .packages-hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .packages-hero h1 {
            font-size: 3rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        .packages-hero p {
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

        /* Package Cards */
        .package-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.4s ease;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            height: 100%;
            border: none;
        }

        .package-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 16px 50px rgba(0,0,0,0.12);
        }

        .package-card .image-wrapper {
            position: relative;
            overflow: hidden;
            height: 220px;
        }

        .package-card .image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .package-card:hover .image-wrapper img {
            transform: scale(1.08);
        }

        .package-card .image-wrapper .badge-top-right {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #f9a825, #fbc02d);
            color: #1a1a2e;
            padding: 5px 16px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.75rem;
            box-shadow: 0 4px 15px rgba(249, 168, 37, 0.4);
            z-index: 2;
        }

        .package-card .image-wrapper .badge-top-right i {
            margin-right: 5px;
        }

        .package-card .image-wrapper .price-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
            color: #fff;
        }

        .package-card .image-wrapper .price-overlay .price {
            font-size: 1.6rem;
            font-weight: 800;
        }

        .package-card .image-wrapper .price-overlay .price small {
            font-size: 0.9rem;
            font-weight: 400;
            opacity: 0.8;
        }

        .package-card .body-content {
            padding: 25px;
        }

        .package-card .body-content h5 {
            font-weight: 700;
            font-size: 1.2rem;
            color: #1a1a2e;
            margin-bottom: 10px;
        }

        .package-card .body-content .meta {
            display: flex;
            gap: 15px;
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .package-card .body-content .meta i {
            color: #1f6332;
            margin-right: 5px;
        }

        .package-card .body-content .meta .badge-duration {
            background: #e8f5e9;
            color: #1f6332;
            padding: 3px 12px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .package-card .body-content .meta .badge-featured {
            background: #fff3e0;
            color: #f57c00;
            padding: 3px 12px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .package-card .body-content p {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .package-card .body-content .destination {
            color: #1a1a2e;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .package-card .body-content .destination i {
            color: #dc3545;
            margin-right: 5px;
        }

        .package-card .body-content .inclusions {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 3px solid #1f6332;
        }

        .package-card .body-content .inclusions strong {
            color: #1a1a2e;
            display: block;
            margin-bottom: 5px;
        }

        .package-card .body-content .inclusions p {
            margin-bottom: 0;
            font-size: 0.85rem;
        }

        .package-card .btn-book {
            background: linear-gradient(135deg, #1f6332, #2e7d32);
            color: #fff;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            border: none;
        }

        .package-card .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(31, 99, 50, 0.4);
            background: linear-gradient(135deg, #2e7d32, #388e3c);
            color: #fff;
        }

        .package-card .btn-detail {
            background: transparent;
            color: #013565;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 2px solid #013565;
            width: 100%;
            margin-bottom: 10px;
        }

        .package-card .btn-detail:hover {
            background: #013565;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(1, 53, 101, 0.2);
        }

        .package-card .btn-group-buttons {
            display: flex;
            flex-direction: column;
            gap: 8px;
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

        /* Filter/Search Section */
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

        /* Responsive */
        @media (max-width: 768px) {
            .packages-hero h1 {
                font-size: 2rem;
            }
            .packages-hero p {
                font-size: 1rem;
            }
            .stat-number {
                font-size: 1.8rem;
            }
            .package-card .image-wrapper {
                height: 180px;
            }
            .filter-section .btn-filter {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .package-card .image-wrapper {
                height: 160px;
            }
            .package-card .body-content {
                padding: 18px;
            }
            .package-card .body-content h5 {
                font-size: 1rem;
            }
            .package-card .body-content .meta {
                font-size: 0.75rem;
                gap: 8px;
            }
            .package-card .image-wrapper .price-overlay .price {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>

<!-- Hero Section -->
<section class="packages-hero">
    <div class="container text-center">
        <h1><i class="fas fa-box me-2"></i> Our Travel Packages</h1>
        <div class="divider"></div>
        <p>Discover our curated travel packages designed for unforgettable Himalayan experiences</p>
    </div>
</section>

<div class="container py-4">
    <!-- Filter/Search Section -->
    <div class="filter-section">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-bold"><i class="fas fa-search me-1"></i> Search Packages</label>
                <input type="text" class="form-control" id="searchPackages" placeholder="Search by name or destination..." onkeyup="filterPackages()">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold"><i class="fas fa-filter me-1"></i> Filter by Destination</label>
                <select class="form-select" id="filterDestination" onchange="filterPackages()">
                    <option value="all">All Destinations</option>
                    <option value="Darjeeling">Darjeeling</option>
                    <option value="Sikkim">Sikkim</option>
                    <option value="Gangtok">Gangtok</option>
                    <option value="Mirik">Mirik</option>
                    <option value="Dooars">Dooars</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn-filter w-100" onclick="resetFilters()">
                    <i class="fas fa-undo me-1"></i> Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Packages Grid -->
    <div class="row" id="packagesGrid">
        <?php foreach($packages as $package): ?>
        <div class="col-lg-4 col-md-6 mb-4 package-item" 
             data-name="<?php echo strtolower($package['name']); ?>" 
             data-destination="<?php echo strtolower($package['destination']); ?>">
            <div class="package-card">
                <div class="image-wrapper">
                    <?php if($package['image_path']): ?>
                    <img src="<?php echo $package['image_path']; ?>" alt="<?php echo $package['name']; ?>">
                    <?php else: ?>
                    <img src="images/package-placeholder.jpg" alt="Package Image">
                    <?php endif; ?>
                    <?php if($package['featured']): ?>
                    <span class="badge-top-right"><i class="fas fa-star"></i> Featured</span>
                    <?php endif; ?>
                    <div class="price-overlay">
                        <span class="price">₹<?php echo number_format($package['price'], 0); ?> <small>/ car</small></span>
                    </div>
                </div>
                <div class="body-content">
                    <h5><?php echo $package['name']; ?></h5>
                    <div class="meta">
                        <span class="badge-duration"><i class="fas fa-clock"></i> <?php echo $package['duration']; ?></span>
                        <?php if($package['featured']): ?>
                        <span class="badge-featured"><i class="fas fa-crown"></i> Featured</span>
                        <?php endif; ?>
                    </div>
                    <p><?php echo truncateText($package['description'], 100); ?></p>
                    <div class="destination">
                        <i class="fas fa-map-marker-alt"></i> <?php echo $package['destination']; ?>
                    </div>
                    <?php if($package['inclusions']): ?>
                    <div class="inclusions">
                        <strong><i class="fas fa-check-circle me-1" style="color: #1f6332;"></i> Inclusions:</strong>
                        <p><?php echo truncateText($package['inclusions'], 60); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Button Group -->
                    <div class="btn-group-buttons">
                        <!-- View Details Button -->
                        <a href="package-detail.php?id=<?php echo $package['id']; ?>" class="btn-detail">
                            <i class="fas fa-info-circle"></i> View Details
                        </a>
                        
                        <!-- Book Now Button -->
                        <a href="contact.php?package=<?php echo $package['id']; ?>" class="btn-book">
                            <i class="fas fa-paper-plane"></i> Book Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- No Results Message -->
    <div id="noResults" class="text-center py-5" style="display: none;">
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h4>No packages found</h4>
        <p class="text-muted">Try adjusting your search or filter criteria</p>
        <button class="btn btn-primary" onclick="resetFilters()" style="border-radius: 50px; padding: 10px 30px; background: linear-gradient(135deg, #1f6332, #2e7d32); border: none;">
            <i class="fas fa-undo me-1"></i> Reset Filters
        </button>
    </div>

    <!-- Call to Action -->
    <div class="text-center mt-5">
        <h3 style="color: #013565; font-weight: 700;">Need a Custom Package?</h3>
        <p class="text-muted">Contact us and we'll create a tailor-made itinerary just for you</p>
        <a href="contact.php" class="btn btn-primary" style="border-radius: 50px; padding: 14px 45px; background: linear-gradient(135deg, #013565, #1f6332); border: none; box-shadow: 0 8px 30px rgba(1, 53, 101, 0.3); transition: all 0.3s ease;">
            <i class="fas fa-headset me-2"></i> Contact Us Now
        </a>
    </div>
</div>

<?php include 'include/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Filter packages
    function filterPackages() {
        const searchInput = document.getElementById('searchPackages');
        const destinationFilter = document.getElementById('filterDestination');
        const searchTerm = searchInput.value.toLowerCase().trim();
        const destination = destinationFilter.value.toLowerCase();
        
        const packages = document.querySelectorAll('.package-item');
        let visibleCount = 0;
        
        packages.forEach(function(item) {
            const name = item.dataset.name || '';
            const itemDestination = item.dataset.destination || '';
            
            const matchesSearch = name.includes(searchTerm) || itemDestination.includes(searchTerm);
            const matchesDestination = destination === 'all' || itemDestination === destination;
            
            if (matchesSearch && matchesDestination) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        const noResults = document.getElementById('noResults');
        if (visibleCount === 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }
    
    // Reset filters
    function resetFilters() {
        document.getElementById('searchPackages').value = '';
        document.getElementById('filterDestination').value = 'all';
        filterPackages();
    }
    
    // Search on Enter key
    document.getElementById('searchPackages').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            filterPackages();
        }
    });
</script>
</body>
</html>