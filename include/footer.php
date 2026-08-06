<?php
// include/footer.php
$settings = getSettings($pdo);
?>
<footer class="footer bg-dark text-light py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5><i class="fas fa-compass"></i> CrossWay Darjeeling Travel</h5>
                <p>Your trusted travel partner for unforgettable Himalayan journeys.</p>
                <p><i class="fas fa-map-marker-alt"></i> Darjeeling, West Bengal</p>
                <p><i class="fas fa-phone"></i> <?php echo $settings['site_phone'] ?? '7797970234'; ?></p>
                <p><i class="fas fa-envelope"></i> <?php echo $settings['site_email'] ?? 'crosswaydarjeelingtravel@gmail.com'; ?></p>
            </div>
            <div class="col-md-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="packages.php" class="text-light">Packages</a></li>
                    <li><a href="gallery.php" class="text-light">Gallery</a></li>
                    <li><a href="about.php" class="text-light">About Us</a></li>
                    <li><a href="contact.php" class="text-light">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>We Offer</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check-circle text-success"></i> Darjeeling Sightseeing</li>
                    <li><i class="fas fa-check-circle text-success"></i> Mirik Sightseeing</li>
                    <li><i class="fas fa-check-circle text-success"></i> Gangtok & Sikkim Tours</li>
                    <li><i class="fas fa-check-circle text-success"></i> Dooars Tour Packages</li>
                    <li><i class="fas fa-check-circle text-success"></i> NJP & Bagdogra Pickup/Drop</li>
                </ul>
            </div>
        </div>
        <hr class="bg-light">
        <div class="text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> CrossWay Darjeeling Travel. All rights reserved.</p>
        </div>
    </div>
</footer>