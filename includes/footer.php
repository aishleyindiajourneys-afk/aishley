<footer class="footer position-relative overflow-hidden">
    <div class="footer-bg" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('uploads/website/bg-1.jpg'); background-size: cover; background-position: center; opacity: 0.05; pointer-events: none;"></div>
    <div class="container position-relative" style="z-index: 1;">
        <div class="row">
            <div class="col-md-3 mb-4">
                <img src="<?= UPLOAD_URL ?>website/logo.png" alt="<?= htmlspecialchars($settings['site_name']) ?>" class="mb-3" style="height: 50px;">
                <p class="text-muted"><?= htmlspecialchars($settings['site_tagline']) ?></p>
                <p class="text-muted small mt-3">We are Registered Tours and Travel Agency and highly qualified Tour Operators in Agra, India. Our mission is to provide our guests with the greatest possible tours and travel experience. We have fifteen years of experience in the travel industry.</p>
            </div>
            <div class="col-md-3 mb-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="tours.php">Tours</a></li>
                    <li><a href="destinations.php">Destinations</a></li>
                    <li><a href="blogs.php">Blog</a></li>
                    <li><a href="gallery.php">Gallery</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
                <div class="mt-3">
                    <img src="<?= UPLOAD_URL ?>footer_image/imgi_55_payment-cash.png" alt="Cash Payment" style="height: 80px;">
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <h5>Contact Info</h5>
                <p class="text-muted"><i class="fas fa-envelope me-2"></i><?= htmlspecialchars($settings['contact_email']) ?></p>
                <p class="text-muted"><i class="fas fa-phone me-2"></i><?= htmlspecialchars($settings['contact_phone']) ?></p>
                <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($settings['contact_address']) ?></p>
                <div class="mt-3">
                    <h6 class="text-muted small mb-2">Our Partners</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <img src="<?= UPLOAD_URL ?>footer_image/imgi_56_ap1.png" alt="Partner 1" style="height: 35px;">
                        <img src="<?= UPLOAD_URL ?>footer_image/imgi_57_ap2.png" alt="Partner 2" style="height: 35px;">
                        <img src="<?= UPLOAD_URL ?>footer_image/imgi_58_ap3.png" alt="Partner 3" style="height: 35px;">
                        <img src="<?= UPLOAD_URL ?>footer_image/imgi_59_ap4.png" alt="Partner 4" style="height: 35px;">
                        <img src="<?= UPLOAD_URL ?>footer_image/imgi_60_ap5.png" alt="Partner 5" style="height: 35px;">
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <h5>Popular Tours</h5>
                <ul class="list-unstyled small">
                    <li><a href="tour-details.php?slug=golden-triangle-tour" class="text-muted">Golden Triangle Tour</a></li>
                    <li><a href="tour-details.php?slug=kerala-backwaters" class="text-muted">Kerala Backwaters</a></li>
                    <li><a href="tour-details.php?slug=rajasthan-heritage" class="text-muted">Rajasthan Heritage</a></li>
                    <li><a href="tour-details.php?slug=goa-beach-vacation" class="text-muted">Goa Beach Vacation</a></li>
                </ul>
                <div class="mt-3">
                    <h6 class="text-muted small mb-2">Payment Methods</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <img src="<?= UPLOAD_URL ?>footer_image/imgi_51_paypal.png" alt="PayPal" style="height: 25px;">
                        <img src="<?= UPLOAD_URL ?>footer_image/imgi_52_mastercard.png" alt="Mastercard" style="height: 25px;">
                        <img src="<?= UPLOAD_URL ?>footer_image/imgi_53_visa.png" alt="Visa" style="height: 25px;">
                    </div>
                </div>
                <div class="mt-3">
                    <?php if ($settings['social_facebook']): ?><a href="<?= $settings['social_facebook'] ?>" class="me-2"><i class="fab fa-facebook fa-lg"></i></a><?php endif; ?>
                    <?php if ($settings['social_twitter']): ?><a href="<?= $settings['social_twitter'] ?>" class="me-2"><i class="fab fa-twitter fa-lg"></i></a><?php endif; ?>
                    <?php if ($settings['social_instagram']): ?><a href="<?= $settings['social_instagram'] ?>" class="me-2"><i class="fab fa-instagram fa-lg"></i></a><?php endif; ?>
                    <?php if ($settings['social_youtube']): ?><a href="<?= $settings['social_youtube'] ?>" class="me-2"><i class="fab fa-youtube fa-lg"></i></a><?php endif; ?>
                </div>
            </div>
        </div>
        <hr class="my-4 border-secondary">
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="text-muted mb-0">&copy; <?= date('Y') ?> <?= htmlspecialchars($settings['site_name']) ?>. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="page.php?slug=privacy-policy" class="text-muted me-3">Privacy Policy</a>
                <a href="page.php?slug=terms-conditions" class="text-muted">Terms & Conditions</a>
            </div>
        </div>
    </div>
</footer>
