<?php
// index.php
require_once 'config/database.php';
require_once 'include/functions.php';

$settings = getSettings($pdo);
$page = getPage($pdo, 'home');
$featured_packages = getPackages($pdo, 3, true);
$gallery_images = getGallery($pdo, 6);
$sightseeing = getSightseeing($pdo); // Add this line

include 'include/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $settings['site_name'] ?? 'CrossWay Travel'; ?> - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* ============================================
           ADDITIONAL STYLES FOR INDEX.PHP
           ============================================ */

        /* Override body padding - remove since navbar is not fixed */
        body {
            padding-top: 0;
        }

        /* Hero Carousel - Full Width */
        .hero-wrapper {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
        }

        .hero-carousel {
            position: absolute;
            inset: 0;
            z-index: 0;
        }

        .hero-carousel .carousel-item {
            height: 100vh;
            min-height: 600px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .hero-carousel .carousel-item::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15, 76, 58, 0.85) 0%, rgba(15, 76, 58, 0.6) 100%);
            z-index: 1;
        }

        .hero-carousel .carousel-item .slide-content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            padding: 20px;
        }

        .hero-carousel .carousel-item .slide-content .slide-label {
            display: inline-block;
            background: rgba(249, 168, 37, 0.2);
            backdrop-filter: blur(10px);
            padding: 6px 20px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 20px;
            border: 1px solid rgba(249, 168, 37, 0.2);
            color: #f9a825;
        }

        .hero-carousel .carousel-item .slide-content h2 {
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 15px;
            text-shadow: 0 4px 30px rgba(0,0,0,0.3);
        }

        .hero-carousel .carousel-item .slide-content h2 .highlight {
            color: #f9a825;
        }

        .hero-carousel .carousel-item .slide-content p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto 30px;
            line-height: 1.8;
        }

        .hero-carousel .carousel-item .slide-content .btn-slide {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #f9a825, #fbc02d);
            color: #1a1a2e;
            padding: 14px 40px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 8px 30px rgba(249, 168, 37, 0.4);
        }

        .hero-carousel .carousel-item .slide-content .btn-slide:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(249, 168, 37, 0.6);
            color: #1a1a2e;
        }

        /* Carousel Controls */
        .hero-carousel .carousel-control-prev,
        .hero-carousel .carousel-control-next {
            width: 60px;
            height: 60px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.15);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .hero-wrapper:hover .hero-carousel .carousel-control-prev,
        .hero-wrapper:hover .hero-carousel .carousel-control-next {
            opacity: 1;
        }

        .hero-carousel .carousel-control-prev {
            left: 30px;
        }

        .hero-carousel .carousel-control-next {
            right: 30px;
        }

        .hero-carousel .carousel-control-prev:hover,
        .hero-carousel .carousel-control-next:hover {
            background: rgba(249, 168, 37, 0.3);
            border-color: #f9a825;
        }

        /* Carousel Indicators */
        .hero-carousel .carousel-indicators {
            bottom: 40px;
            z-index: 10;
        }

        .hero-carousel .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.5);
            margin: 0 6px;
            transition: all 0.3s ease;
        }

        .hero-carousel .carousel-indicators button.active {
            background: #f9a825;
            border-color: #f9a825;
            transform: scale(1.2);
        }

        /* Scroll Down Indicator */
        .scroll-down {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            color: rgba(255,255,255,0.6);
            font-size: 0.8rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            animation: bounceDown 2s infinite;
            text-decoration: none;
        }

        .scroll-down i {
            font-size: 1.4rem;
        }

        @keyframes bounceDown {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(10px); }
        }

        /* Features Section */
        .features-section {
            margin-top: -40px;
            position: relative;
            z-index: 2;
        }

        .feature-box .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
            color: #fff;
            transition: all 0.3s ease;
        }

        .feature-box:hover .icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }

        /* Packages Section */
        .packages-section {
            background: #f8f9fa;
            padding: 70px 0;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: #1a1a2e;
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #1f6332, #8bc34a);
            border-radius: 2px;
        }

        .section-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto 40px;
        }

        .section-title .highlight {
            background: linear-gradient(135deg, #f9a825, #fbc02d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .package-card-modern {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            height: 100%;
        }

        .package-card-modern:hover {
            transform: translateY(-12px);
            box-shadow: 0 16px 50px rgba(0,0,0,0.12);
        }

        .package-card-modern .image-wrapper {
            position: relative;
            overflow: hidden;
            height: 220px;
        }

        .package-card-modern .image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .package-card-modern:hover .image-wrapper img {
            transform: scale(1.08);
        }

        .package-card-modern .image-wrapper .badge-top-right {
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
        }

        .package-card-modern .image-wrapper .badge-top-right i {
            margin-right: 5px;
        }

        .package-card-modern .image-wrapper .price-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
            color: #fff;
        }

        .package-card-modern .image-wrapper .price-overlay .price {
            font-size: 1.6rem;
            font-weight: 800;
        }

        .package-card-modern .image-wrapper .price-overlay .price small {
            font-size: 0.9rem;
            font-weight: 400;
            opacity: 0.8;
        }

        .package-card-modern .body-content {
            padding: 25px;
        }

        .package-card-modern .body-content h5 {
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .package-card-modern .body-content .meta {
            display: flex;
            gap: 15px;
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .package-card-modern .body-content .meta i {
            color: #2e7d32;
            margin-right: 5px;
        }

        .package-card-modern .body-content p {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .package-card-modern .body-content .btn-view {
            background: transparent;
            color: #2e7d32;
            padding: 10px 30px;
            border-radius: 50px;
            border: 2px solid #2e7d32;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .package-card-modern .body-content .btn-view:hover {
            background: #2e7d32;
            color: #fff;
            transform: translateX(5px);
        }

        /* Sightseeing Section - Dynamic */
        .sightseeing-section {
            background: #fff;
            padding: 70px 0;
        }

        .sightseeing-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
            transition: all 0.3s ease;
            height: 100%;
        }

        .sightseeing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        }

        .sightseeing-card .card-header-custom {
            background: linear-gradient(135deg, #1f6332, #2e7d32);
            color: #fff;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
        }

        .sightseeing-card .card-header-custom .count-badge {
            background: rgba(255,255,255,0.2);
            padding: 4px 14px;
            border-radius: 50px;
            font-size: 0.75rem;
        }

        .sightseeing-card .point-item {
            padding: 12px 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background 0.2s ease;
        }

        .sightseeing-card .point-item:last-child {
            border-bottom: none;
        }

        .sightseeing-card .point-item:hover {
            background: #f8f9fa;
        }

        .sightseeing-card .point-icon {
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

        .sightseeing-card .point-name {
            font-weight: 500;
            color: #333;
        }

        /* Gallery Section */
        .gallery-section {
            background: #f8f9fa;
            padding: 70px 0;
        }

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
            aspect-ratio: 1;
        }

        .gallery-item-modern:nth-child(1) {
            grid-column: span 2;
            grid-row: span 2;
        }

        .gallery-item-modern img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .gallery-item-modern:hover img {
            transform: scale(1.08);
        }

        .gallery-item-modern .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(transparent, rgba(15, 76, 58, 0.8));
            opacity: 0;
            transition: opacity 0.4s ease;
            display: flex;
            align-items: flex-end;
            padding: 25px;
            color: #fff;
        }

        .gallery-item-modern:hover .overlay {
            opacity: 1;
        }

        .gallery-item-modern .overlay h6 {
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .gallery-item-modern .overlay small {
            opacity: 0.8;
        }

        .gallery-item-modern .overlay .icon-expand {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .gallery-item-modern:hover .overlay .icon-expand {
            background: #f9a825;
            color: #1a1a2e;
            transform: rotate(90deg);
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

        /* Testimonials */
        .testimonials-section {
            background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%);
            padding: 70px 0;
        }

        .testimonial-card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 30px;
            color: #fff;
            transition: all 0.3s ease;
            height: 100%;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.12);
            border-color: rgba(139, 195, 74, 0.3);
        }

        .testimonial-card .stars {
            color: #f5b342;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .testimonial-card .stars i {
            margin-right: 3px;
        }

        .testimonial-card .testimonial-text {
            font-size: 0.95rem;
            line-height: 1.7;
            opacity: 0.9;
            margin-bottom: 20px;
            font-style: italic;
        }

        .testimonial-card .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .testimonial-card .testimonial-author img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(139, 195, 74, 0.4);
        }

        .testimonial-card .testimonial-author .author-name {
            font-weight: 600;
            font-size: 1rem;
        }

        .testimonial-card .testimonial-author .author-location {
            font-size: 0.8rem;
            opacity: 0.6;
        }

        /* CTA Section */
        .cta-section {
            padding: 80px 0;
        }

        .cta-section .cta-box {
            background: linear-gradient(135deg, #0f4c3a 0%, #1a6b52 50%, #2d8f6f 100%);
            border-radius: 20px;
            padding: 60px;
            color: #fff;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .cta-section .cta-box::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .cta-section .cta-box h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .cta-section .cta-box h2 .highlight {
            color: #f9a825;
        }

        .cta-section .cta-box p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 500px;
            margin: 0 auto 30px;
        }

        .cta-section .btn-cta {
            background: linear-gradient(135deg, #f9a825, #fbc02d);
            color: #1a1a2e;
            padding: 16px 50px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 30px rgba(249, 168, 37, 0.3);
            border: none;
        }

        .cta-section .btn-cta:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 40px rgba(249, 168, 37, 0.5);
            color: #1a1a2e;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .hero-carousel .carousel-item .slide-content h2 {
                font-size: 3rem;
            }

            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .gallery-item-modern:nth-child(1) {
                grid-column: span 1;
                grid-row: span 1;
            }

            .hero-carousel .carousel-control-prev,
            .hero-carousel .carousel-control-next {
                width: 45px;
                height: 45px;
                opacity: 0.7;
            }

            .hero-carousel .carousel-control-prev {
                left: 15px;
            }

            .hero-carousel .carousel-control-next {
                right: 15px;
            }

            .cta-section .cta-box {
                padding: 40px 25px;
            }

            .lightbox-overlay .lightbox-nav {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .hero-carousel .carousel-item .slide-content h2 {
                font-size: 2.2rem;
            }

            .hero-carousel .carousel-item .slide-content p {
                font-size: 1rem;
            }

            .hero-carousel .carousel-item {
                min-height: 500px;
                height: 500px;
            }

            .hero-wrapper {
                min-height: 580px;
            }

            .gallery-grid {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .scroll-down {
                display: none;
            }

            .cta-section .cta-box h2 {
                font-size: 2rem;
            }

            .lightbox-overlay {
                padding: 20px;
            }

            .lightbox-overlay .lightbox-close {
                top: 20px;
                right: 20px;
                width: 45px;
                height: 45px;
                font-size: 1.8rem;
            }

            .section-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 576px) {
            .hero-carousel .carousel-item .slide-content h2 {
                font-size: 1.8rem;
            }

            .hero-carousel .carousel-item .slide-content .btn-slide {
                padding: 12px 30px;
                font-size: 0.9rem;
            }

            .hero-carousel .carousel-item {
                min-height: 400px;
                height: 400px;
            }

            .hero-wrapper {
                min-height: 480px;
            }

            .gallery-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .gallery-item-modern {
                aspect-ratio: 1;
            }

            .gallery-item-modern .overlay {
                padding: 15px;
            }

            .gallery-item-modern .overlay h6 {
                font-size: 0.85rem;
            }

            .section-title {
                font-size: 1.6rem;
            }

            .cta-section .cta-box h2 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>

<!-- ==========================================
     NAVBAR (Included from include/navbar.php)
     ========================================== -->

<!-- ==========================================
     HERO WITH CAROUSEL
     ========================================== -->
<div class="hero-wrapper">
    <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active" style="background-image: url('images/c1.jpg');">
                <div class="slide-content">
                    <div>
                        <span class="slide-label"><i class="fas fa-mountain"></i> Destination</span>
                        <h2>Welcome to <span class="highlight">Darjeeling</span></h2>
                        <p>Discover the Queen of the Hills with breathtaking views of the Himalayas and lush tea gardens.</p>
                        <a href="packages.php" class="btn-slide">
                            <i class="fas fa-compass"></i> Explore Packages
                        </a>
                    </div>
                </div>
            </div>
            <div class="carousel-item" style="background-image: url('images/c4.jpg');">
                <div class="slide-content">
                    <div>
                        <span class="slide-label"><i class="fas fa-sun"></i> Sunrise</span>
                        <h2>Witness the <span class="highlight">Sunrise</span> at Tiger Hill</h2>
                        <p>Experience the magical sunrise over Kanchenjunga and the entire Himalayan range.</p>
                        <a href="packages.php" class="btn-slide">
                            <i class="fas fa-camera"></i> View Tours
                        </a>
                    </div>
                </div>
            </div>
            <div class="carousel-item" style="background-image: url('images/c3.jfif');">
                <div class="slide-content">
                    <div>
                        <span class="slide-label"><i class="fas fa-leaf"></i> Tea Estate</span>
                        <h2>Explore <span class="highlight">Tea Gardens</span></h2>
                        <p>Walk through the lush green tea estates and experience the world-famous Darjeeling tea.</p>
                        <a href="packages.php" class="btn-slide">
                            <i class="fas fa-leaf"></i> Explore Tours
                        </a>
                    </div>
                </div>
            </div>
            <div class="carousel-item" style="background-image: url('images/c2.jpg');">
                <div class="slide-content">
                    <div>
                        <span class="slide-label"><i class="fas fa-route"></i> Adventure</span>
                        <h2>Discover <span class="highlight">Sikkim</span></h2>
                        <p>Explore the mystical beauty of the Himalayas and its vibrant culture with our curated tours.</p>
                        <a href="packages.php" class="btn-slide">
                            <i class="fas fa-route"></i> View Packages
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <a href="#features" class="scroll-down">
        <span>Scroll</span>
        <i class="fas fa-chevron-down"></i>
    </a>
</div>

<!-- ==========================================
     FEATURES SECTION
     ========================================== -->
<section class="features-section" id="features">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-box text-center">
                    <div class="icon-wrapper success">
                        <i class="fas fa-car"></i>
                    </div>
                    <h5>Premium Cabs</h5>
                    <p>Innova Crysta, WagonR, Dzire, Sumo Gold &amp; more for your comfort</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box text-center">
                    <div class="icon-wrapper warning">
                        <i class="fas fa-route"></i>
                    </div>
                    <h5>Customized Tours</h5>
                    <p>Family, couple &amp; group tours tailored to your preferences</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box text-center">
                    <div class="icon-wrapper info">
                        <i class="fas fa-hotel"></i>
                    </div>
                    <h5>Best Hotels</h5>
                    <p>Comfortable stays in Darjeeling, Sikkim &amp; across the Himalayas</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box text-center">
                    <div class="icon-wrapper primary">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h5>24/7 Support</h5>
                    <p>Round-the-clock assistance for all your travel needs</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     PACKAGES SECTION
     ========================================== -->
<section class="packages-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Featured <span class="highlight">Packages</span></h2>
            <p class="section-subtitle">Handpicked tours for an unforgettable Himalayan experience</p>
        </div>
        <div class="row g-4">
            <?php foreach($featured_packages as $package): ?>
            <div class="col-md-4">
                <div class="package-card-modern">
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
                            <span class="price">₹<?php echo number_format($package['price'], 0); ?> <small>/ person</small></span>
                        </div>
                    </div>
                    <div class="body-content">
                        <h5><?php echo $package['name']; ?></h5>
                        <div class="meta">
                            <span><i class="fas fa-clock"></i> <?php echo $package['duration']; ?></span>
                            <span><i class="fas fa-map-marker-alt"></i> <?php echo $package['destination']; ?></span>
                        </div>
                        <p><?php echo truncateText($package['description'], 70); ?></p>
                        <a href="packages.php" class="btn-view">View Details <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="packages.php" class="btn btn-primary" style="border-radius: 50px; padding: 16px 45px; background: linear-gradient(135deg, #1f6332, #2e7d32); border: none; box-shadow: 0 8px 30px rgba(31, 99, 50, 0.4); transition: all 0.3s ease;">
                <i class="fas fa-box"></i> View All Packages
            </a>
        </div>
    </div>
</section>

<!-- ==========================================
     SIGHTSEEING / DESTINATIONS SECTION - DYNAMIC
     ========================================== -->
<section class="sightseeing-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Top <span class="highlight">Destinations</span></h2>
            <p class="section-subtitle">Must-visit places in Darjeeling and beyond</p>
        </div>
        <div class="row g-4">
            <?php foreach($sightseeing as $item): ?>
            <div class="col-md-4">
                <div class="sightseeing-card">
                    <div class="card-header-custom" style="background: linear-gradient(135deg, <?php echo $item['color']; ?>, <?php echo $item['color']; ?>cc);">
                        <span><i class="fas <?php echo $item['icon']; ?> me-2"></i> <?php echo $item['title']; ?></span>
                        <span class="count-badge"><?php echo $item['badge']; ?></span>
                    </div>
                    <?php 
                    $points = explode("\n", trim($item['points']));
                    $pointNumber = 1;
                    foreach($points as $point): 
                        if(trim($point) != ''):
                    ?>
                    <div class="point-item">
                        <span class="point-icon"><?php echo $pointNumber; ?></span>
                        <span class="point-name"><?php echo htmlspecialchars(trim($point)); ?></span>
                    </div>
                    <?php 
                        $pointNumber++;
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==========================================
     GALLERY SECTION
     ========================================== -->
<section class="gallery-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Photo <span class="highlight">Gallery</span></h2>
            <p class="section-subtitle">Click on any image to view in full screen</p>
        </div>
        <div class="gallery-grid">
            <?php 
            $gallery_data = array_slice($gallery_images, 0, 4);
            foreach($gallery_data as $index => $image): 
            ?>
            <div class="gallery-item-modern" data-index="<?php echo $index; ?>" onclick="openLightbox(<?php echo $index; ?>)">
                <img src="<?php echo $image['image_path']; ?>" alt="<?php echo $image['title']; ?>">
                <div class="overlay">
                    <div>
                        <h6><?php echo $image['title']; ?></h6>
                        <small><?php echo $image['category'] ?? 'Himalayan Beauty'; ?></small>
                    </div>
                    <div class="icon-expand">
                        <i class="fas fa-expand"></i>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="gallery.php" class="btn btn-primary" style="border-radius: 50px; padding: 16px 45px; background: linear-gradient(135deg, #1f6332, #2e7d32); border: none; box-shadow: 0 8px 30px rgba(31, 99, 50, 0.4); transition: all 0.3s ease;">
                <i class="fas fa-images"></i> View Full Gallery
            </a>
        </div>
    </div>
</section>

<!-- ==========================================
     LIGHTBOX OVERLAY
     ========================================== -->
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
            <span id="lightboxTitle"></span> <small id="lightboxCategory"></small>
        </div>
    </div>
</div>

<!-- ==========================================
     TESTIMONIALS SECTION
     ========================================== -->
<section class="testimonials-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; color: #fff; font-weight: 700;">
                What Our <span style="color: #f9a825;">Travelers</span> Say
            </h2>
            <p style="color: rgba(255,255,255,0.7); font-size: 1.1rem;">Real stories from real travelers</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"Absolutely amazing experience! CrossWay Travel made our Darjeeling trip unforgettable. The driver was professional and the itinerary was perfectly planned."</p>
                    <div class="testimonial-author">
                        <img src="images/avatar1.jpg" alt="Customer">
                        <div>
                            <div class="author-name">Rahul Sharma</div>
                            <div class="author-location">Delhi, India</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"The best travel agency in Darjeeling! They took care of everything from pickup to drop. The vehicle was comfortable and the driver was very knowledgeable."</p>
                    <div class="testimonial-author">
                        <img src="images/avatar2.jpg" alt="Customer">
                        <div>
                            <div class="author-name">Priya Patel</div>
                            <div class="author-location">Mumbai, India</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"Highly recommended! CrossWay Travel provided excellent service for our group tour. The customized package was perfect and within our budget."</p>
                    <div class="testimonial-author">
                        <img src="images/avatar3.jpg" alt="Customer">
                        <div>
                            <div class="author-name">Amit Kumar</div>
                            <div class="author-location">Kolkata, India</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     CTA SECTION
     ========================================== -->
<section class="cta-section">
    <div class="container">
        <div class="cta-box">
            <h2>Ready to Explore the <span class="highlight">Himalayas</span>?</h2>
            <p>Book your dream holiday with CrossWay Travel and create memories that last a lifetime.</p>
            <a href="contact.php" class="btn-cta">
                <i class="fas fa-calendar-check"></i> Book Your Trip Now
            </a>
        </div>
    </div>
</section>

<?php include 'include/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Lightbox - Gallery Image Click
    <?php 
    $gallery_json = json_encode(array_slice($gallery_images, 0, 4));
    ?>
    const galleryData = <?php echo $gallery_json; ?>;
    let currentLightboxIndex = 0;

    function openLightbox(index) {
        currentLightboxIndex = index;
        const overlay = document.getElementById('lightboxOverlay');
        const img = document.getElementById('lightboxImage');
        const title = document.getElementById('lightboxTitle');
        const category = document.getElementById('lightboxCategory');
        
        const item = galleryData[index];
        img.src = item.image_path;
        title.textContent = item.title;
        category.textContent = item.category || 'Himalayan Beauty';
        
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
        
        const item = galleryData[currentLightboxIndex];
        
        const content = document.querySelector('.lightbox-content');
        content.style.animation = 'none';
        setTimeout(() => {
            content.style.animation = 'lightboxZoomIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
        }, 10);
        
        img.src = item.image_path;
        title.textContent = item.title;
        category.textContent = item.category || 'Himalayan Beauty';
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

    // Carousel - Ensure carousel works
    document.addEventListener('DOMContentLoaded', function() {
        const carouselElement = document.getElementById('heroCarousel');
        if (carouselElement) {
            const carousel = new bootstrap.Carousel(carouselElement, {
                interval: 5000,
                pause: 'hover',
                wrap: true,
                ride: 'carousel'
            });
        }
    });
</script>
</body>
</html>