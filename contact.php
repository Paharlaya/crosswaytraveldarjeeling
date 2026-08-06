<?php
// contact.php
require_once 'config/database.php';
require_once 'include/functions.php';

$settings = getSettings($pdo);
$page = getPage($pdo, 'contact');

$message = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message_text = trim($_POST['message'] ?? '');

    if(empty($name) || empty($email) || empty($subject) || empty($message_text)) {
        $error = 'Please fill all required fields.';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO contacts (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $subject, $message_text]);
            $message = 'Thank you! Your message has been sent successfully. We will get back to you soon.';
        } catch(PDOException $e) {
            $error = 'An error occurred. Please try again later.';
        }
    }
}

include 'include/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $settings['site_name'] ?? 'CrossWay Travel'; ?> - Contact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* ============================================
           CONTACT PAGE STYLES
           ============================================ */
        
        /* Hero Section */
        .contact-hero {
            background: linear-gradient(135deg, #013565 0%, #1f6332 100%);
            color: white;
            padding: 60px 0;
            border-radius: 15px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }
        .contact-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .contact-hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .contact-hero h1 {
            font-size: 3rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        .contact-hero p {
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

        /* Contact Cards */
        .contact-card {
            background: #fff;
            border-radius: 16px;
            padding: 25px 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
            transition: all 0.3s ease;
            height: 100%;
            text-align: center;
        }

        .contact-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.1);
            border-color: #1f6332;
        }

        .contact-card .icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 24px;
            color: #fff;
            transition: all 0.3s ease;
        }

        .contact-card:hover .icon-circle {
            transform: scale(1.1) rotate(5deg);
        }

        .contact-card .icon-circle.phone {
            background: linear-gradient(135deg, #1f6332, #2e7d32);
        }

        .contact-card .icon-circle.email {
            background: linear-gradient(135deg, #013565, #1a4a7a);
        }

        .contact-card .icon-circle.location {
            background: linear-gradient(135deg, #d32f2f, #e53935);
        }

        .contact-card .icon-circle.whatsapp {
            background: linear-gradient(135deg, #25D366, #128C7E);
        }

        .contact-card h5 {
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 5px;
            font-size: 1rem;
        }

        .contact-card p {
            color: #6c757d;
            margin-bottom: 0;
            font-size: 0.85rem;
        }

        .contact-card p a {
            color: #1f6332;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .contact-card p a:hover {
            color: #013565;
            text-decoration: underline;
        }

        /* Contact Form */
        .form-card {
            background: #fff;
            border-radius: 16px;
            padding: 35px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
            height: 100%;
        }

        .form-card h3 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: #013565;
            margin-bottom: 10px;
            font-size: 1.6rem;
        }

        .form-card .form-label {
            font-weight: 600;
            color: #1a1a2e;
            font-size: 0.85rem;
        }

        .form-card .form-label .required {
            color: #dc3545;
        }

        .form-card .form-control {
            border-radius: 10px;
            padding: 10px 15px;
            border-color: #e0e0e0;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .form-card .form-control:focus {
            border-color: #1f6332;
            box-shadow: 0 0 0 0.2rem rgba(31, 99, 50, 0.1);
        }

        .form-card .form-control::placeholder {
            color: #adb5bd;
            font-size: 0.85rem;
        }

        .form-card textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .form-card .btn-submit {
            background: linear-gradient(135deg, #1f6332, #2e7d32);
            color: #fff;
            padding: 12px 40px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            transition: all 0.3s ease;
            width: 100%;
            box-shadow: 0 8px 30px rgba(31, 99, 50, 0.3);
        }

        .form-card .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(31, 99, 50, 0.4);
            background: linear-gradient(135deg, #2e7d32, #388e3c);
        }

        /* Quick Response Box */
        .quick-response-box {
            background: #fff;
            border-radius: 16px;
            padding: 20px 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .quick-response-box .icon-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f9a825, #fbc02d);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #1a1a2e;
            flex-shrink: 0;
        }

        .quick-response-box .content {
            flex: 1;
        }

        .quick-response-box .content h5 {
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 2px;
            font-size: 1rem;
        }

        .quick-response-box .content p {
            color: #6c757d;
            margin-bottom: 0;
            font-size: 0.85rem;
        }

        .quick-response-box .btn-group-quick {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .quick-response-box .btn-group-quick .btn-call {
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            background: linear-gradient(135deg, #1f6332, #2e7d32);
            color: #fff;
            border: none;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .quick-response-box .btn-group-quick .btn-call:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(31, 99, 50, 0.3);
        }

        .quick-response-box .btn-group-quick .btn-whatsapp {
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: #fff;
            border: none;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .quick-response-box .btn-group-quick .btn-whatsapp:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(37, 211, 102, 0.3);
        }

        /* Map Section - Increased Height */
        .map-section {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
            height: 100%;
            min-height: 400px;
        }

        .map-section iframe {
            width: 100%;
            height: 100%;
            min-height: 400px;
            border: none;
            display: block;
        }

        /* Right Column - Make it stretch */
        .right-column {
            display: flex;
            flex-direction: column;
        }

        .right-column .quick-response-box {
            flex-shrink: 0;
        }

        .right-column .map-section {
            flex: 1;
        }

        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 40px 0;
            border-radius: 12px;
            margin: 30px 0;
            clear: both;
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

        /* Alert Messages */
        .alert-custom {
            border-radius: 12px;
            padding: 15px 20px;
            border: none;
            margin-bottom: 25px;
        }

        .alert-custom.alert-success {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            color: #1f6332;
            border-left: 4px solid #1f6332;
        }

        .alert-custom.alert-danger {
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            color: #c62828;
            border-left: 4px solid #c62828;
        }

        .alert-custom .btn-close {
            filter: grayscale(1);
        }

        /* Responsive */
        @media (max-width: 991px) {
            .quick-response-box {
                flex-direction: column;
                text-align: center;
            }
            .quick-response-box .btn-group-quick {
                justify-content: center;
                width: 100%;
            }
            .quick-response-box .btn-group-quick .btn-call,
            .quick-response-box .btn-group-quick .btn-whatsapp {
                flex: 1;
                justify-content: center;
            }
            .map-section {
                min-height: 300px;
            }
            .map-section iframe {
                min-height: 300px;
            }
        }

        @media (max-width: 768px) {
            .contact-hero h1 {
                font-size: 2rem;
            }
            .contact-hero p {
                font-size: 1rem;
            }
            .stat-number {
                font-size: 1.8rem;
            }
            .form-card {
                padding: 25px;
            }
            .contact-card {
                padding: 20px 15px;
            }
            .contact-card .icon-circle {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
            .quick-response-box {
                padding: 15px 20px;
                flex-direction: column;
                text-align: center;
            }
            .quick-response-box .btn-group-quick {
                width: 100%;
                flex-direction: column;
            }
            .quick-response-box .btn-group-quick .btn-call,
            .quick-response-box .btn-group-quick .btn-whatsapp {
                width: 100%;
                justify-content: center;
            }
            .map-section {
                min-height: 250px;
            }
            .map-section iframe {
                min-height: 250px;
            }
        }

        @media (max-width: 576px) {
            .form-card {
                padding: 20px;
            }
            .form-card h3 {
                font-size: 1.3rem;
            }
            .form-card .btn-submit {
                padding: 10px 25px;
                font-size: 0.9rem;
            }
            .contact-card .icon-circle {
                width: 45px;
                height: 45px;
                font-size: 18px;
            }
            .contact-card h5 {
                font-size: 0.9rem;
            }
            .contact-card p {
                font-size: 0.75rem;
            }
            .map-section {
                min-height: 200px;
            }
            .map-section iframe {
                min-height: 200px;
            }
            .quick-response-box .icon-wrapper {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
            .quick-response-box .content h5 {
                font-size: 0.9rem;
            }
            .quick-response-box .content p {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>

<!-- Hero Section -->
<section class="contact-hero">
    <div class="container text-center">
        <h1><i class="fas fa-envelope me-2"></i> Contact Us</h1>
        <div class="divider"></div>
        <p>We'd love to hear from you! Reach out to us for any inquiries or assistance</p>
    </div>
</section>

<div class="container py-4">
    <!-- Alert Messages -->
    <?php if($message): ?>
    <div class="alert-custom alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <?php if($error): ?>
    <div class="alert-custom alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Contact Info Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="contact-card">
                <div class="icon-circle phone">
                    <i class="fas fa-phone"></i>
                </div>
                <h5>Phone</h5>
                <p><a href="tel:<?php echo htmlspecialchars($settings['site_phone'] ?? '7797970234'); ?>"><?php echo htmlspecialchars($settings['site_phone'] ?? '7797970234'); ?></a></p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="contact-card">
                <div class="icon-circle email">
                    <i class="fas fa-envelope"></i>
                </div>
                <h5>Email</h5>
                <p><a href="mailto:<?php echo htmlspecialchars($settings['site_email'] ?? 'crosswaydarjeelingtravel@gmail.com'); ?>"><?php echo htmlspecialchars($settings['site_email'] ?? 'crosswaydarjeelingtravel@gmail.com'); ?></a></p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="contact-card">
                <div class="icon-circle location">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h5>Address</h5>
                <p><?php echo htmlspecialchars($settings['address'] ?? 'Darjeeling, West Bengal, India'); ?></p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="contact-card">
                <div class="icon-circle whatsapp">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <h5>WhatsApp</h5>
                <p><a href="https://wa.me/91<?php echo str_replace(['+', ' ', '-'], '', htmlspecialchars($settings['site_phone'] ?? '7797970234')); ?>" target="_blank">Chat with us</a></p>
            </div>
        </div>
    </div>

    <!-- Contact Form & Map -->
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="form-card">
                <h3><i class="fas fa-paper-plane me-2" style="color: #1f6332;"></i> Send Us a Message</h3>
                <p style="color: #6c757d; margin-bottom: 25px; font-size: 0.95rem;">Fill out the form below and we'll get back to you within 24 hours.</p>
                
                <form method="POST" action="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="required">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Your full name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address <span class="required">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" placeholder="+91 98765 43210">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Subject <span class="required">*</span></label>
                            <input type="text" name="subject" class="form-control" placeholder="What is this regarding?" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Message <span class="required">*</span></label>
                            <textarea name="message" rows="5" class="form-control" placeholder="Tell us how we can help you..." required></textarea>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-paper-plane me-2"></i> Send Message
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-5 right-column">
            <!-- Quick Response Box -->
            <div class="quick-response-box">
                <div class="icon-wrapper">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="content">
                    <h5>Quick Response</h5>
                    <p>We respond within 24 hours</p>
                </div>
                <div class="btn-group-quick">
                    <a href="tel:<?php echo htmlspecialchars($settings['site_phone'] ?? '7797970234'); ?>" class="btn-call">
                        <i class="fas fa-phone"></i> Call
                    </a>
                    <a href="https://wa.me/91<?php echo str_replace(['+', ' ', '-'], '', htmlspecialchars($settings['site_phone'] ?? '7797970234')); ?>" target="_blank" class="btn-whatsapp">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>

            <!-- Map - Now stretches to fill remaining height -->
            <div class="map-section">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14212.099307041426!2d88.2622903!3d27.0417411!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39e42f6b5c5d1b0d%3A0x5d9a5f7a0e5b0d1!2sDarjeeling%2C%20West%20Bengal!5e0!3m2!1sen!2sin!4v1700000000000" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="stats-section mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-6">
                    <div class="stat-item">
                        <span class="stat-icon"><i class="fas fa-smile"></i></span>
                        <div class="stat-number">5000+</div>
                        <div class="stat-label">Happy Customers</div>
                    </div>
                </div>
              
                <div class="col-md-4 col-6">
                    <div class="stat-item">
                        <span class="stat-icon"><i class="fas fa-star"></i></span>
                        <div class="stat-number">4.9★</div>
                        <div class="stat-label">Customer Rating</div>
                    </div>
                </div>
                <div class="col-md-4 col-6">
                    <div class="stat-item">
                        <span class="stat-icon"><i class="fas fa-reply-all"></i></span>
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Response Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="text-center mt-5">
        <h3 style="color: #013565; font-weight: 700;">Prefer to Book Over the Phone?</h3>
        <p class="text-muted">Call us directly and we'll help you plan your perfect Himalayan getaway</p>
        <a href="tel:<?php echo htmlspecialchars($settings['site_phone'] ?? '7797970234'); ?>" class="btn btn-primary" style="border-radius: 50px; padding: 14px 45px; background: linear-gradient(135deg, #013565, #1f6332); border: none; box-shadow: 0 8px 30px rgba(1, 53, 101, 0.3); transition: all 0.3s ease;">
            <i class="fas fa-phone me-2"></i> Call Now: <?php echo htmlspecialchars($settings['site_phone'] ?? '7797970234'); ?>
        </a>
    </div>
</div>

<?php include 'include/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert-custom');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                const closeBtn = alert.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.click();
                }
            }, 5000);
        });
    });
</script>
</body>
</html>