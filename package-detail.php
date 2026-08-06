<?php
// package-detail.php
require_once 'config/database.php';
require_once 'include/functions.php';

$settings = getSettings($pdo);
$page = getPage($pdo, 'package-detail');

// Get package ID from URL
$packageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($packageId > 0) {
    $package = getPackageById($pdo, $packageId);
} else {
    $package = null;
}

// If package not found, redirect to packages page
if (!$package) {
    header('Location: packages.php');
    exit;
}

include 'include/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $settings['site_name'] ?? 'CrossWay Travel'; ?> - <?php echo $package['name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Package Detail Styles */
        .detail-hero {
            background: linear-gradient(135deg, #013565 0%, #1f6332 100%);
            color: white;
            padding: 40px 0;
            border-radius: 15px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }
        .detail-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .detail-hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        .detail-hero .breadcrumb {
            background: transparent;
            padding: 0;
            position: relative;
            z-index: 1;
        }
        .detail-hero .breadcrumb-item a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }
        .detail-hero .breadcrumb-item.active {
            color: #fff;
        }
        .detail-hero .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255,255,255,0.5);
        }

        .package-detail-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .package-detail-card .detail-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }

        .package-detail-card .detail-content {
            padding: 30px;
        }

        .package-detail-card .detail-price {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1f6332;
        }

        .package-detail-card .detail-price small {
            font-size: 1rem;
            font-weight: 400;
            color: #6c757d;
        }

        .package-detail-card .detail-meta {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin: 20px 0;
            padding: 20px 0;
            border-top: 1px solid #e9ecef;
            border-bottom: 1px solid #e9ecef;
        }

        .package-detail-card .detail-meta .meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .package-detail-card .detail-meta .meta-item i {
            color: #1f6332;
            font-size: 1.2rem;
        }

        .package-detail-card .detail-meta .meta-item span {
            color: #495057;
            font-weight: 500;
        }

        .package-detail-card .detail-description {
            color: #495057;
            line-height: 1.8;
            font-size: 1.05rem;
            margin: 20px 0;
        }

        .package-detail-card .detail-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin: 20px 0;
        }

        .package-detail-card .detail-section.inclusions {
            border-left: 4px solid #1f6332;
        }

        .package-detail-card .detail-section.exclusions {
            border-left: 4px solid #dc3545;
        }

        .package-detail-card .detail-section h5 {
            color: #013565;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .package-detail-card .detail-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .package-detail-card .detail-section ul li {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .package-detail-card .detail-section ul li:last-child {
            border-bottom: none;
        }

        .package-detail-card .detail-section ul li i {
            width: 20px;
        }

        .package-detail-card .detail-section.inclusions ul li i {
            color: #1f6332;
        }

        .package-detail-card .detail-section.exclusions ul li i {
            color: #dc3545;
        }

        .package-detail-card .detail-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .package-detail-card .btn-book-now {
            background: linear-gradient(135deg, #1f6332, #2e7d32);
            color: #fff;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: none;
        }

        .package-detail-card .btn-book-now:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(31, 99, 50, 0.4);
            color: #fff;
        }

        .package-detail-card .btn-back {
            background: transparent;
            color: #013565;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 2px solid #013565;
            transition: all 0.3s ease;
        }

        .package-detail-card .btn-back:hover {
            background: #013565;
            color: #fff;
        }

        .badge-featured-lg {
            background: linear-gradient(135deg, #f9a825, #fbc02d);
            color: #1a1a2e;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            display: inline-block;
            margin-top: 10px;
        }

        .badge-featured-lg i {
            margin-right: 8px;
        }

        /* Package Card for Related */
        .package-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 2px 15px rgba(0,0,0,0.06);
            height: 100%;
            border: 1px solid #f0f0f0;
        }

        .package-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }

        .package-card .image-wrapper {
            position: relative;
            overflow: hidden;
            height: 180px;
        }

        .package-card .image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .package-card .image-wrapper .price-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 15px;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
            color: #fff;
        }

        .package-card .image-wrapper .price-overlay .price {
            font-size: 1.3rem;
            font-weight: 700;
        }

        .package-card .body-content {
            padding: 20px;
        }

        .package-card .body-content h6 {
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 8px;
        }

        .package-card .body-content p {
            color: #6c757d;
            font-size: 0.85rem;
            margin-bottom: 12px;
        }

        @media (max-width: 768px) {
            .package-detail-card .detail-image {
                height: 250px;
            }
            .package-detail-card .detail-price {
                font-size: 2rem;
            }
            .detail-hero h1 {
                font-size: 1.8rem;
            }
            .package-detail-card .detail-actions {
                flex-direction: column;
            }
            .package-detail-card .btn-book-now,
            .package-detail-card .btn-back {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<!-- Hero Section -->
<section class="detail-hero">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="packages.php">Packages</a></li>
                <li class="breadcrumb-item active"><?php echo $package['name']; ?></li>
            </ol>
        </nav>
        <h1><i class="fas fa-box me-2"></i> Package Details</h1>
    </div>
</section>

<div class="container py-4">
    <div class="row">
        <div class="col-lg-12">
            <!-- Package Detail Card -->
            <div class="package-detail-card">
                <?php if($package['image_path']): ?>
                <img src="<?php echo $package['image_path']; ?>" alt="<?php echo $package['name']; ?>" class="detail-image">
                <?php endif; ?>
                
                <div class="detail-content">
                    <div class="row">
                        <div class="col-md-8">
                            <h2><?php echo $package['name']; ?></h2>
                            <?php if($package['featured']): ?>
                            <span class="badge-featured-lg"><i class="fas fa-star"></i> Featured Package</span>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="detail-price">
                                Rs<?php echo number_format($package['price'], 0); ?>
                                <small>/ <?php echo $package['price_unit'] ?? 'car'; ?></small>
                            </div>
                        </div>
                    </div>

                    <!-- Meta Information -->
                    <div class="detail-meta">
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span>Duration: <?php echo $package['duration']; ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Destination: <?php echo $package['destination']; ?></span>
                        </div>
                        <?php if(!empty($package['group_size'])): ?>
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            <span>Group Size: <?php echo $package['group_size']; ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($package['tour_type'])): ?>
                        <div class="meta-item">
                            <i class="fas fa-tag"></i>
                            <span>Type: <?php echo $package['tour_type']; ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($package['featured']): ?>
                        <div class="meta-item">
                            <i class="fas fa-crown" style="color: #f9a825;"></i>
                            <span style="color: #f57c00;">Featured Package</span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Description -->
                    <div class="detail-description">
                        <h5><i class="fas fa-align-left me-2" style="color: #013565;"></i> Package Description</h5>
                        <p><?php echo nl2br($package['description']); ?></p>
                    </div>

                    <!-- Itinerary (if available) -->
                    <?php if(!empty($package['itinerary'])): ?>
                    <div class="detail-section" style="border-left: 4px solid #013565;">
                        <h5><i class="fas fa-route me-2" style="color: #013565;"></i> Itinerary</h5>
                        <ul>
                            <?php 
                            $itineraryArray = preg_split('/[,|\n]+/', $package['itinerary']);
                            foreach($itineraryArray as $day): 
                                $day = trim($day);
                                if(!empty($day)):
                            ?>
                            <li><i class="fas fa-calendar-day" style="color: #013565;"></i> <?php echo $day; ?></li>
                            <?php 
                                endif; 
                            endforeach; 
                            ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Inclusions -->
                    <?php if(!empty($package['inclusions'])): ?>
                    <div class="detail-section inclusions">
                        <h5><i class="fas fa-check-circle me-2" style="color: #1f6332;"></i> What's Included</h5>
                        <ul>
                            <?php 
                            // Split inclusions by comma or newline
                            $inclusionsArray = preg_split('/[,|\n]+/', $package['inclusions']);
                            foreach($inclusionsArray as $inclusion): 
                                $inclusion = trim($inclusion);
                                if(!empty($inclusion)):
                            ?>
                            <li><i class="fas fa-check-circle"></i> <?php echo $inclusion; ?></li>
                            <?php 
                                endif; 
                            endforeach; 
                            ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Exclusions -->
                    <?php if(!empty($package['exclusions'])): ?>
                    <div class="detail-section exclusions">
                        <h5><i class="fas fa-times-circle me-2" style="color: #dc3545;"></i> What's Not Included</h5>
                        <ul>
                            <?php 
                            $exclusionsArray = preg_split('/[,|\n]+/', $package['exclusions']);
                            foreach($exclusionsArray as $exclusion): 
                                $exclusion = trim($exclusion);
                                if(!empty($exclusion)):
                            ?>
                            <li><i class="fas fa-times-circle"></i> <?php echo $exclusion; ?></li>
                            <?php 
                                endif; 
                            endforeach; 
                            ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Additional Info -->
                    <?php if(!empty($package['additional_info'])): ?>
                    <div class="detail-section" style="border-left: 4px solid #f9a825;">
                        <h5><i class="fas fa-info-circle me-2" style="color: #f9a825;"></i> Additional Information</h5>
                        <p style="margin-bottom: 0;"><?php echo nl2br($package['additional_info']); ?></p>
                    </div>
                    <?php endif; ?>

                    <!-- Terms & Conditions -->
                    <?php if(!empty($package['terms'])): ?>
                    <div class="detail-section" style="border-left: 4px solid #6c757d;">
                        <h5><i class="fas fa-file-contract me-2" style="color: #6c757d;"></i> Terms & Conditions</h5>
                        <ul>
                            <?php 
                            $termsArray = preg_split('/[,|\n]+/', $package['terms']);
                            foreach($termsArray as $term): 
                                $term = trim($term);
                                if(!empty($term)):
                            ?>
                            <li><i class="fas fa-file-signature" style="color: #6c757d;"></i> <?php echo $term; ?></li>
                            <?php 
                                endif; 
                            endforeach; 
                            ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="detail-actions">
                        <a href="contact.php?package=<?php echo $package['id']; ?>" class="btn-book-now">
                            <i class="fas fa-paper-plane"></i> Book This Package Now
                        </a>
                        <a href="packages.php" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Back to Packages
                        </a>
                    </div>
                </div>
            </div>

            <!-- Related Packages Section -->
            <div class="mt-5">
                <h3 class="mb-4" style="color: #013565; font-weight: 700;">
                    <i class="fas fa-suitcase me-2"></i> You Might Also Like
                </h3>
                <div class="row">
                    <?php 
                    // Get related packages (same destination or featured)
                    $relatedPackages = getRelatedPackages($pdo, $package['id'], $package['destination']);
                    foreach($relatedPackages as $related): 
                    ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="package-card">
                            <div class="image-wrapper">
                                <?php if($related['image_path']): ?>
                                <img src="<?php echo $related['image_path']; ?>" alt="<?php echo $related['name']; ?>">
                                <?php else: ?>
                                <img src="images/package-placeholder.jpg" alt="Package Image">
                                <?php endif; ?>
                                <div class="price-overlay">
                                    <span class="price">Rs<?php echo number_format($related['price'], 0); ?></span>
                                </div>
                            </div>
                            <div class="body-content">
                                <h6><?php echo $related['name']; ?></h6>
                                <p><?php echo truncateText($related['description'], 60); ?></p>
                                <a href="package-detail.php?id=<?php echo $related['id']; ?>" class="btn btn-sm btn-outline-primary" style="border-radius: 50px; border-color: #013565; color: #013565; width: 100%;">
                                    View Details <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>