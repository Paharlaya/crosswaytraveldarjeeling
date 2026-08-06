<?php
// include/navbar.php
$current_page = basename($_SERVER['PHP_SELF']);
$settings = getSettings($pdo);
?>
<!DOCTYPE html>
<html>
<head>
<style>
/* ============================================
   TOP BAR STYLES
   ============================================ */
.top-bar {
    background: #0d1b2a;
    color: #a8b2c1;
    padding: 6px 0;
    font-size: 0.8rem;
    border-bottom: 2px solid #1f6332;
    position: relative;
    z-index: 1030;
}
.top-bar a {
    color: #a8b2c1;
    text-decoration: none;
    transition: all 0.3s ease;
}
.top-bar a:hover {
    color: #ffffff;
}
.top-bar .divider {
    color: #2a3f4f;
    margin: 0 8px;
}
.top-bar .top-contact i {
    color: #8bc34a;
    margin-right: 5px;
    width: 16px;
}
.top-bar .social-icons a {
    display: inline-block;
    width: 30px;
    height: 30px;
    line-height: 30px;
    text-align: center;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
    transition: all 0.3s ease;
    margin-left: 4px;
    font-size: 0.8rem;
    color: #a8b2c1;
}
.top-bar .social-icons a:hover {
    background: #1f6332;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(31, 99, 50, 0.3);
}

/* ============================================
   MAIN NAVBAR STYLES
   ============================================ */
.main-navbar {
    background: linear-gradient(135deg, #0d1b2a 0%, #1a2a3a 100%);
    padding: 10px 0;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    position: sticky;
    top: 0;
    z-index: 1029;
    transition: all 0.3s ease;
}
.main-navbar.scrolled {
    padding: 6px 0;
    background: rgba(13, 27, 42, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 30px rgba(0,0,0,0.5);
}
.main-navbar .navbar-brand {
    display: flex;
    align-items: center;
    padding: 0;
}
.main-navbar .logo-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #1f6332, #2e7d32);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 1.6rem;
    color: #fff;
    transition: all 0.5s ease;
    flex-shrink: 0;
    box-shadow: 0 4px 15px rgba(31, 99, 50, 0.3);
}
.main-navbar .logo-icon i {
    transform: rotate(0deg);
    transition: transform 0.6s ease;
}
.main-navbar .navbar-brand:hover .logo-icon {
    transform: scale(1.05);
    box-shadow: 0 6px 25px rgba(31, 99, 50, 0.5);
}
.main-navbar .navbar-brand:hover .logo-icon i {
    transform: rotate(360deg);
}
.main-navbar .logo-text {
    display: flex;
    flex-direction: column;
    line-height: 1.1;
}
.main-navbar .logo-text .brand-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: 0.5px;
    font-family: 'Playfair Display', serif;
}
.main-navbar .logo-text .brand-name .highlight {
    color: #8bc34a;
}
.main-navbar .logo-text .brand-tagline {
    font-size: 0.65rem;
    color: #8bc34a;
    letter-spacing: 2px;
    text-transform: uppercase;
    font-weight: 300;
    opacity: 0.8;
}

/* Nav Links */
.main-navbar .nav-link {
    color: rgba(255,255,255,0.75) !important;
    font-weight: 500;
    padding: 10px 18px !important;
    border-radius: 8px;
    transition: all 0.3s ease;
    position: relative;
    font-size: 0.9rem;
}
.main-navbar .nav-link i {
    margin-right: 8px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}
.main-navbar .nav-link:hover {
    color: #ffffff !important;
    background: rgba(255,255,255,0.06);
}
.main-navbar .nav-link:hover i {
    transform: translateY(-2px);
    color: #8bc34a;
}
.main-navbar .nav-link.active {
    color: #ffffff !important;
    background: rgba(31, 99, 50, 0.2);
}
.main-navbar .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: 2px;
    left: 50%;
    transform: translateX(-50%);
    width: 25px;
    height: 3px;
    background: #8bc34a;
    border-radius: 2px;
    animation: slideIn 0.3s ease;
}
@keyframes slideIn {
    from { width: 0; opacity: 0; }
    to { width: 25px; opacity: 1; }
}

/* Book Now Button */
.main-navbar .btn-book {
    background: linear-gradient(135deg, #1f6332, #2e7d32);
    border: none;
    color: #ffffff;
    padding: 8px 25px;
    border-radius: 25px;
    transition: all 0.3s ease;
    font-weight: 600;
    font-size: 0.85rem;
    box-shadow: 0 4px 15px rgba(31, 99, 50, 0.3);
}
.main-navbar .btn-book:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(31, 99, 50, 0.5);
    color: #ffffff;
    background: linear-gradient(135deg, #2e7d32, #388e3c);
}
.main-navbar .btn-book i {
    margin-right: 6px;
}

/* Admin Button */
.main-navbar .btn-admin {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.15);
    color: rgba(255,255,255,0.7);
    padding: 6px 16px;
    border-radius: 20px;
    transition: all 0.3s ease;
    font-size: 0.8rem;
}
.main-navbar .btn-admin:hover {
    background: rgba(255,255,255,0.1);
    color: #ffffff;
    border-color: rgba(255,255,255,0.3);
}
.main-navbar .btn-admin i {
    margin-right: 4px;
    font-size: 0.7rem;
}

/* Notification Badge */
.main-navbar .nav-badge {
    position: absolute;
    top: 2px;
    right: 2px;
    background: #dc3545;
    color: #fff;
    font-size: 0.55rem;
    padding: 1px 6px;
    border-radius: 10px;
    font-weight: 700;
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* Mobile Toggle */
.main-navbar .navbar-toggler {
    border: 2px solid rgba(255,255,255,0.2);
    padding: 6px 10px;
    border-radius: 8px;
}
.main-navbar .navbar-toggler:focus {
    box-shadow: 0 0 0 3px rgba(31, 99, 50, 0.3);
}
.main-navbar .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,0.8)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}

/* ============================================
   RESPONSIVE STYLES
   ============================================ */
@media (max-width: 991px) {
    .top-bar .top-contact {
        text-align: center;
        margin-bottom: 4px;
        font-size: 0.7rem;
    }
    .top-bar .social-icons {
        text-align: center;
    }
    .top-bar .social-icons a {
        width: 26px;
        height: 26px;
        line-height: 26px;
        font-size: 0.7rem;
    }
    .main-navbar .navbar-brand {
        flex: 1;
    }
    .main-navbar .logo-text .brand-name {
        font-size: 1.2rem;
    }
    .main-navbar .logo-text .brand-tagline {
        font-size: 0.55rem;
    }
    .main-navbar .logo-icon {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
        margin-right: 10px;
    }
    .main-navbar .nav-link.active::after {
        display: none;
    }
    .main-navbar .nav-link {
        padding: 8px 15px !important;
    }
    .main-navbar .btn-book,
    .main-navbar .btn-admin {
        margin-top: 5px;
        width: 100%;
        text-align: center;
    }
    .main-navbar .nav-badge {
        position: relative;
        top: -2px;
        right: 0;
        margin-left: 5px;
    }
}

@media (max-width: 576px) {
    .top-bar {
        font-size: 0.6rem;
        padding: 4px 0;
    }
    .top-bar .divider {
        margin: 0 3px;
    }
    .top-bar .top-contact i {
        width: 12px;
        font-size: 0.6rem;
    }
    .top-bar .social-icons a {
        width: 22px;
        height: 22px;
        line-height: 22px;
        font-size: 0.6rem;
    }
    .main-navbar {
        padding: 6px 0;
    }
    .main-navbar .logo-text .brand-name {
        font-size: 1rem;
    }
    .main-navbar .logo-text .brand-tagline {
        font-size: 0.5rem;
        letter-spacing: 1px;
    }
    .main-navbar .logo-icon {
        width: 32px;
        height: 32px;
        font-size: 0.9rem;
        margin-right: 8px;
    }
}
</style>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- ==========================================
     TOP BAR - Contact & Social
     ========================================== -->
<div class="top-bar">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8 col-12">
                <div class="top-contact">
                    <i class="fas fa-phone"></i>
                    <a href="tel:<?php echo htmlspecialchars($settings['site_phone'] ?? '7797970234'); ?>">
                        <?php echo htmlspecialchars($settings['site_phone'] ?? '7797970234'); ?>
                    </a>
                    <span class="divider">|</span>
                    <i class="fas fa-envelope"></i>
                    <a href="mailto:<?php echo htmlspecialchars($settings['site_email'] ?? 'crosswaydarjeelingtravel@gmail.com'); ?>">
                        <?php echo htmlspecialchars($settings['site_email'] ?? 'crosswaydarjeelingtravel@gmail.com'); ?>
                    </a>
                    <span class="divider">|</span>
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?php echo htmlspecialchars($settings['address'] ?? 'Darjeeling, West Bengal'); ?></span>
                    <span class="divider">|</span>
                    <i class="fas fa-clock"></i>
                    <span>24/7 Support</span>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="social-icons text-md-end text-center">
                    <?php if(!empty($settings['facebook'])): ?>
                    <a href="<?php echo htmlspecialchars($settings['facebook']); ?>" target="_blank" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <?php endif; ?>
                    <?php if(!empty($settings['instagram'])): ?>
                    <a href="<?php echo htmlspecialchars($settings['instagram']); ?>" target="_blank" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <?php endif; ?>
                    <?php if(!empty($settings['twitter'])): ?>
                    <a href="<?php echo htmlspecialchars($settings['twitter']); ?>" target="_blank" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <?php endif; ?>
                    <a href="https://wa.me/91<?php echo str_replace(['+', ' ', '-'], '', htmlspecialchars($settings['site_phone'] ?? '7797970234')); ?>" target="_blank" title="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="#" target="_blank" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================
     MAIN NAVBAR
     ========================================== -->
<nav class="navbar navbar-expand-lg main-navbar" id="mainNavbar">
    <div class="container">
        <!-- Logo / Brand -->
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
           
            <div class="logo-text">
                <img src="images/logo.jpeg" style="width:100px">
            </div>
        </a>
        
        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="fa fa-bars"></span>
        </button>
        
        <!-- Navigation Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
			 <li class="nav-item">
                    <a href="<?php echo ADMIN_URL; ?>login.php" class="btn btn-admin" title="Admin Login">
                        <i class="fas fa-lock"></i> Admin
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'packages.php' ? 'active' : ''; ?>" href="packages.php">
                        <i class="fas fa-box"></i> Packages
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'gallery.php' ? 'active' : ''; ?>" href="gallery.php">
                        <i class="fas fa-images"></i> Gallery
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'about.php' ? 'active' : ''; ?>" href="about.php">
                        <i class="fas fa-info-circle"></i> About
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>" href="contact.php">
                        <i class="fas fa-envelope"></i> Contact
                    </a>
                </li>
                
                <!-- Book Now Button -->
                <li class="nav-item ms-lg-2">
                    <a href="contact.php" class="btn btn-book">
                        <i class="fas fa-calendar-check"></i> Book Now
                    </a>
                </li>
                
                <!-- Admin Button (small, subtle) -->
                <li class="nav-item">
                    <a href="admin/login.php" class="btn btn-admin" title="Admin Login">
                        <i class="fas fa-lock"></i> Admin
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ==========================================
     SCRIPTS
     ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ---- Navbar Scroll Effect ----
    const navbar = document.getElementById('mainNavbar');
    let lastScroll = 0;
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
        
        if (currentScroll > 80) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });
    
    // ---- Mobile Menu Auto-Close ----
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    const navbarCollapse = document.getElementById('navbarNav');
    
    navLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (navbarCollapse.classList.contains('show')) {
                navbarCollapse.classList.remove('show');
            }
        });
    });
    
    // ---- Smooth Nav Link Animation ----
    navLinks.forEach(function(link) {
        link.addEventListener('mouseenter', function() {
            if (!this.classList.contains('active')) {
                this.style.transform = 'translateY(-2px)';
                this.style.color = '#ffffff';
            }
        });
        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // ---- Logo Animation on Load ----
    const logoIcon = document.querySelector('.logo-icon');
    if (logoIcon) {
        logoIcon.style.transition = 'all 0.6s ease';
    }
});
</script>

<!-- Bootstrap JS (required for mobile menu) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>