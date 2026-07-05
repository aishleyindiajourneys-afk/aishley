<?php
require_once 'config/database.php';
require_once 'config/functions.php';

$settings = getSettings();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= generateMetaTags('Contact Us', 'Get in touch with Aishley India Journeys for your travel inquiries and bookings.', 'contact, travel inquiry, booking') ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #667eea; --secondary: #764ba2; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; }
        .navbar { background: rgba(255,255,255,0.95) !important; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: 700; color: var(--primary) !important; }
        .nav-link { color: #333 !important; font-weight: 500; }
        .nav-link:hover { color: var(--primary) !important; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--secondary)); border: none; }
        .btn-primary:hover { background: linear-gradient(135deg, var(--secondary), var(--primary)); }
        .page-header {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9)), url('https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=1920') center/cover;
            color: white;
            padding: 100px 0;
        }
        .contact-card { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); padding: 30px; }
        .footer { background: #1a1a2e; color: white; padding: 60px 0 30px; }
    </style>
</head>
<body>
    <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x" style="z-index: 9999;"><?= $flash['message'] ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?= SITE_URL ?>"><i class="fas fa-route me-2"></i><?= htmlspecialchars($settings['site_name']) ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="tours.php">Tours</a></li>
                    <li class="nav-item"><a class="nav-link" href="destinations.php">Destinations</a></li>
                    <li class="nav-item"><a class="nav-link" href="blogs.php">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="gallery.php">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item"><a class="nav-link" href="user/dashboard.php"><i class="fas fa-user me-1"></i>My Account</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <section class="page-header">
        <div class="container">
            <h1 class="display-4 fw-bold">Contact Us</h1>
            <p class="lead">Get in touch with us for your travel inquiries</p>
        </div>
    </section>
    
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="contact-card h-100">
                        <h4 class="mb-4">Contact Information</h4>
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Email</h6>
                                    <p class="text-muted mb-0"><?= htmlspecialchars($settings['contact_email']) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Phone</h6>
                                    <p class="text-muted mb-0"><?= htmlspecialchars($settings['contact_phone']) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Address</h6>
                                    <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($settings['contact_address'])) ?></p>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h5 class="mb-3">Follow Us</h5>
                        <div>
                            <?php if ($settings['social_facebook']): ?><a href="<?= $settings['social_facebook'] ?>" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-facebook"></i></a><?php endif; ?>
                            <?php if ($settings['social_twitter']): ?><a href="<?= $settings['social_twitter'] ?>" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-twitter"></i></a><?php endif; ?>
                            <?php if ($settings['social_instagram']): ?><a href="<?= $settings['social_instagram'] ?>" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-instagram"></i></a><?php endif; ?>
                            <?php if ($settings['social_youtube']): ?><a href="<?= $settings['social_youtube'] ?>" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-youtube"></i></a><?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="contact-card">
                        <h4 class="mb-4">Send us a Message</h4>
                        <form action="controllers/EnquiryController.php?action=create" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Name *</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone *</label>
                                    <input type="text" class="form-control" name="phone" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Subject</label>
                                    <input type="text" class="form-control" name="subject">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Message *</label>
                                <textarea class="form-control" name="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i>Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><?= htmlspecialchars($settings['site_name']) ?></h5>
                    <p class="text-muted"><?= htmlspecialchars($settings['site_tagline']) ?></p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?= SITE_URL ?>">Home</a></li>
                        <li><a href="tours.php">Tours</a></li>
                        <li><a href="destinations.php">Destinations</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Contact</h5>
                    <p class="text-muted"><i class="fas fa-envelope me-2"></i><?= htmlspecialchars($settings['contact_email']) ?></p>
                    <p class="text-muted"><i class="fas fa-phone me-2"></i><?= htmlspecialchars($settings['contact_phone']) ?></p>
                </div>
            </div>
            <hr class="my-4 border-secondary">
            <p class="text-center text-muted mb-0">&copy; <?= date('Y') ?> <?= htmlspecialchars($settings['site_name']) ?>. All rights reserved.</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
