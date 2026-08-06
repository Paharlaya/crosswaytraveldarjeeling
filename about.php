<?php
// about.php
require_once 'config/database.php';
require_once 'include/functions.php';

$settings = getSettings($pdo);
$page = getPage($pdo, 'about');

// Get about settings
$stmt = $pdo->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'about_%'");
$stmt->execute();
$about_settings = [];
while($row = $stmt->fetch()) {
    $about_settings[$row['setting_key']] = $row['setting_value'];
}

include 'include/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($settings['site_name'] ?? 'CrossWay Travel'); ?> - About</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .about-hero {
            background: linear-gradient(135deg, #013565 0%, #1f6332 100%);
            color: white;
            padding: 60px 0;
            border-radius: 15px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }
        .about-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .about-hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .about-hero h1 {
            font-size: 3rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        .about-hero p {
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
        .mission-card, .vision-card, .values-card {
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
        }
        .mission-card {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-bottom: 4px solid #1976d2;
        }
        .vision-card {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            border-bottom: 4px solid #388e3c;
        }
        .values-card {
            background: linear-gradient(135deg, #fff3e0, #ffe0b2);
            border-bottom: 4px solid #f57c00;
        }
        .mission-card:hover, .vision-card:hover, .values-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .mission-card i, .vision-card i, .values-card i {
            font-size: 3rem;
            margin-bottom: 15px;
            display: block;
        }
        .mission-card i { color: #1976d2; }
        .vision-card i { color: #388e3c; }
        .values-card i { color: #f57c00; }
        .service-item {
            padding: 15px 20px;
            background: white;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            border-left: 4px solid #1f6332;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .service-item:hover {
            transform: translateX(8px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .service-item i {
            color: #1f6332;
            margin-right: 10px;
        }
        .fleet-badge {
            padding: 10px 20px;
            background: white;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            display: inline-block;
            width: 100%;
            text-align: center;
            font-weight: 600;
            color: #1f6332;
        }
        .fleet-badge:hover {
            border-color: #1f6332;
            background: #f0f7f0;
            transform: scale(1.05);
        }
        .fleet-badge i {
            margin-right: 8px;
            color: #1f6332;
        }
        .about-content {
            font-size: 1.05rem;
            line-height: 1.8;
            color: #2d3436;
        }
        .about-content h2, .about-content h3 {
            color: #013565;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }
        .about-content h2 {
            font-size: 2rem;
        }
        .about-content h3 {
            font-size: 1.5rem;
        }
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
        .contact-info-box {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            border-left: 4px solid #1f6332;
        }
        .contact-info-box p {
            margin-bottom: 10px;
        }
        .contact-info-box i {
            width: 30px;
            color: #1f6332;
        }
        @media (max-width: 768px) {
            .about-hero h1 { font-size: 2rem; }
            .about-hero p { font-size: 1rem; }
            .stat-number { font-size: 1.8rem; }
        }
    </style>
</head>
<body>

<!-- Hero Section -->
<section class="about-hero">
    <div class="container text-center">
        <h1><i class="fas fa-compass me-2"></i> About CrossWay Darjeeling Travel</h1>
        <div class="divider"></div>
        <p>Your Trusted Partner for Unforgettable Himalayan Journeys</p>
    </div>
</section>

<div class="container py-4">
    <!-- Main Content -->
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <?php if($page && !empty($page['content'])): ?>
            <div class="about-content">
                <?php echo $page['content']; ?>
            </div>
            <?php else: ?>
            
            <!-- Introduction -->
            <div class="about-content">
                <p style="font-size: 1.1rem; line-height: 1.8;">
                    Welcome to <strong>CrossWay Darjeeling Travel</strong>, your trusted partner for unforgettable Himalayan journeys. 
                    Based in the picturesque hill station of Darjeeling, West Bengal, we specialize in crafting exceptional travel 
                    experiences across the Eastern Himalayas. With years of industry expertise and a deep-rooted passion for hospitality, 
                    we are dedicated to showcasing the breathtaking beauty, rich culture, and warm traditions of this magnificent 
                    region to travelers from around the world.
                </p>
            </div>

            <!-- Mission, Vision, Values -->
            <div class="row g-4 mt-4">
                <div class="col-md-4">
                    <div class="mission-card">
                        <i class="fas fa-bullseye"></i>
                        <h4>Our Mission</h4>
                        <p style="color: #2d3436;">
                            <?php echo htmlspecialchars($about_settings['about_mission'] ?? 'To provide authentic, safe, and memorable travel experiences that connect people with the natural wonders and cultural heritage of the Himalayas.'); ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="vision-card">
                        <i class="fas fa-eye"></i>
                        <h4>Our Vision</h4>
                        <p style="color: #2d3436;">
                            <?php echo htmlspecialchars($about_settings['about_vision'] ?? 'To become the most trusted and preferred travel partner in the region, known for quality service and genuine hospitality.'); ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="values-card">
                        <i class="fas fa-heart"></i>
                        <h4>Core Values</h4>
                        <p style="color: #2d3436;">
                            <?php 
                            $values = explode(',', $about_settings['about_values'] ?? 'Integrity, Customer First, Quality Service, Innovation, Sustainability');
                            echo implode(' • ', array_map('trim', $values));
                            ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Our Services -->
            <div class="mt-5">
                <h2 style="color: #013565; text-align: center; margin-bottom: 30px;">
                    <i class="fas fa-concierge-bell me-2" style="color: #1f6332;"></i> Our Services
                </h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="service-item">
                            <i class="fas fa-mountain"></i> Darjeeling Sightseeing
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="service-item">
                            <i class="fas fa-tree"></i> Mirik Sightseeing
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="service-item">
                            <i class="fas fa-city"></i> Gangtok &amp; Sikkim Tours
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="service-item">
                            <i class="fas fa-road"></i> Dooars Tour Packages
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="service-item">
                            <i class="fas fa-taxi"></i> NJP &amp; Bagdogra Pickup/Drop
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="service-item">
                            <i class="fas fa-users"></i> Customized Family, Couple &amp; Group Tours
                        </div>
                    </div>
                </div>
            </div>

            <!-- Our Fleet -->
            <div class="mt-5">
                <h2 style="color: #013565; text-align: center; margin-bottom: 30px;">
                    <i class="fas fa-car me-2" style="color: #1f6332;"></i> Our Fleet
                </h2>
                <div class="row g-3">
                    <div class="col-4 col-md-2">
                        <div class="fleet-badge">
                            <i class="fas fa-car"></i> Innova Crysta
                        </div>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="fleet-badge">
                            <i class="fas fa-car"></i> Innova
                        </div>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="fleet-badge">
                            <i class="fas fa-car"></i> WagonR
                        </div>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="fleet-badge">
                            <i class="fas fa-car"></i> Dzire
                        </div>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="fleet-badge">
                            <i class="fas fa-truck"></i> Sumo Gold (9 Seater)
                        </div>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="fleet-badge">
                            <i class="fas fa-truck"></i> Bolero (9 Seater)
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="stats-section mt-5">
                <div class="container">
                    <div class="row">
                        <div class="col-md-3 col-6">
                            <div class="stat-item">
                                <span class="stat-icon"><i class="fas fa-smile"></i></span>
                                <div class="stat-number" data-count="5000">5000+</div>
                                <div class="stat-label">Happy Customers</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-item">
                                <span class="stat-icon"><i class="fas fa-map-marked-alt"></i></span>
                                <div class="stat-number" data-count="100">100+</div>
                                <div class="stat-label">Tour Packages</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-item">
                                <span class="stat-icon"><i class="fas fa-route"></i></span>
                                <div class="stat-number" data-count="50">50+</div>
                                <div class="stat-label">Destinations</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-item">
                                <span class="stat-icon"><i class="fas fa-star"></i></span>
                                <div class="stat-number" data-count="10">10+</div>
                                <div class="stat-label">Years Experience</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Why Choose Us -->
            <div class="mt-5">
                <h2 style="color: #013565; text-align: center; margin-bottom: 30px;">
                    <i class="fas fa-check-circle me-2" style="color: #1f6332;"></i> Why Choose Us
                </h2>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex">
                            <i class="fas fa-shield-alt fa-2x me-3" style="color: #1f6332;"></i>
                            <div>
                                <h5>Trusted &amp; Reliable</h5>
                                <p class="text-muted">With years of experience, we have built a reputation for trust and reliability.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex">
                            <i class="fas fa-user-friends fa-2x me-3" style="color: #1f6332;"></i>
                            <div>
                                <h5>Personalized Service</h5>
                                <p class="text-muted">Every journey is unique, and we tailor our packages to your preferences.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex">
                            <i class="fas fa-rupee-sign fa-2x me-3" style="color: #1f6332;"></i>
                            <div>
                                <h5>Best Value</h5>
                                <p class="text-muted">Competitive prices without compromising on quality and comfort.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex">
                            <i class="fas fa-headset fa-2x me-3" style="color: #1f6332;"></i>
                            <div>
                                <h5>24/7 Support</h5>
                                <p class="text-muted">We are always available to assist you before, during, and after your trip.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="mt-5">
                <h2 style="color: #013565; text-align: center; margin-bottom: 30px;">
                    <i class="fas fa-address-card me-2" style="color: #1f6332;"></i> Get in Touch
                </h2>
                <div class="contact-info-box">
                    <p><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> <?php echo htmlspecialchars($settings['address'] ?? 'Darjeeling, West Bengal'); ?></p>
                    <p><i class="fas fa-phone"></i> <strong>Phone:</strong> <a href="tel:<?php echo htmlspecialchars($settings['site_phone'] ?? '7797970234'); ?>"><?php echo htmlspecialchars($settings['site_phone'] ?? '7797970234'); ?></a></p>
                    <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($settings['site_email'] ?? 'crosswaydarjeelingtravel@gmail.com'); ?>"><?php echo htmlspecialchars($settings['site_email'] ?? 'crosswaydarjeelingtravel@gmail.com'); ?></a></p>
                    <p><i class="fab fa-whatsapp"></i> <strong>WhatsApp:</strong> <a href="https://wa.me/91<?php echo str_replace(['+', ' ', '-'], '', htmlspecialchars($settings['site_phone'] ?? '7797970234')); ?>">Chat with us</a></p>
                </div>
            </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>