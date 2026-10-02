<?php
require_once 'config/database.php';
require_once 'config/functions.php';

$settings = getSettings();
$flash = getFlash();

// Get gallery items
$stmt = $pdo->query("SELECT * FROM gallery WHERE status = 'active' ORDER BY sort_order ASC, created_at DESC");
$gallery = $stmt->fetchAll();

$pageTitle = "Gallery";
$pageDescription = "Browse our photo gallery showcasing the beautiful destinations and experiences across India.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= generateMetaTags($pageTitle, $pageDescription, 'travel gallery, India photos, tour images') ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #FF631E; --secondary: #201966; --light-bg: #EAE9E7; --white: #FEFEFE; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; }
        .navbar { background: rgba(255,255,255,0.95) !important; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-brand img { height: 40px; }
        .nav-link { color: #333 !important; font-weight: 500; }
        .nav-link:hover { color: var(--primary) !important; }
        .btn-primary { background: #FF631E; border: none; }
        .btn-primary:hover { background: #e55a1a; }
        .page-header {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9)), url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1920') center/cover;
            color: white;
            padding: 100px 0;
        }
        .gallery-item { border-radius: 15px; overflow: hidden; position: relative; cursor: pointer; }
        .gallery-item img { height: 300px; object-fit: cover; transition: transform 0.3s; }
        .gallery-item:hover img { transform: scale(1.1); }
        .gallery-caption { position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); color: white; padding: 15px; transform: translateY(100%); transition: transform 0.3s; }
        .gallery-item:hover .gallery-caption { transform: translateY(0); }
        .footer { background: #201966; color: #FEFEFE; padding: 60px 0 30px; }
        .footer h5 { color: #FEFEFE; font-weight: 600; margin-bottom: 20px; }
        .footer a { color: #EAE9E7; text-decoration: none; }
        .footer a:hover { color: #FF631E; }
        .footer .text-muted { color: #EAE9E7 !important; }
        .footer p { color: #EAE9E7; }
        .footer i { color: #EAE9E7; }
    </style>
</head>
<body>
    <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x" style="z-index: 9999;"><?= $flash['message'] ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?= SITE_URL ?>"><img src="<?= UPLOAD_URL ?>website/logo.png" alt="<?= htmlspecialchars($settings['site_name']) ?>" style="height: 40px; margin-right: 10px;"><span style="color: #201966; font-weight: 600;"><?= htmlspecialchars($settings['site_name']) ?></span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="tours.php">Tours</a></li>
                    <li class="nav-item"><a class="nav-link" href="destinations.php">Destinations</a></li>
                    <li class="nav-item"><a class="nav-link" href="blogs.php">Blog</a></li>
                    <li class="nav-item"><a class="nav-link active" href="gallery.php">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
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
            <h1 class="display-4 fw-bold">Photo Gallery</h1>
            <p class="lead">Visual journey through India's incredible destinations</p>
        </div>
    </section>
    
    <section class="py-5">
        <div class="container">
            <?php if ($gallery): ?>
                <div class="row">
                    <?php foreach ($gallery as $item): ?>
                        <div class="col-md-4 mb-4">
                            <div class="gallery-item">
                                <img src="<?= UPLOAD_URL . $item['image'] ?>" alt="<?= htmlspecialchars($item['title']) ?>" data-bs-toggle="modal" data-bs-target="#imageModal<?= $item['id'] ?>">
                                <div class="gallery-caption">
                                    <h6><?= htmlspecialchars($item['title']) ?: 'Gallery Image' ?></h6>
                                    <?php if ($item['category']): ?>
                                        <small><?= htmlspecialchars($item['category']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-images fa-4x text-muted mb-3"></i>
                    <h4>No images in gallery</h4>
                    <p class="text-muted">Check back later for new photos.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <?php foreach ($gallery as $item): ?>
        <div class="modal fade" id="imageModal<?= $item['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title"><?= htmlspecialchars($item['title']) ?: 'Gallery Image' ?></h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body text-center">
                        <img src="<?= UPLOAD_URL . $item['image'] ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="img-fluid">
                        <?php if ($item['description']): ?>
                            <p class="mt-3 text-muted"><?= htmlspecialchars($item['description']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <img src="<?= UPLOAD_URL ?>website/logo.png" alt="<?= htmlspecialchars($settings['site_name']) ?>" class="mb-3" style="height: 50px;">
                    <p class="text-muted"><?= htmlspecialchars($settings['site_tagline']) ?></p>
                    <div class="mt-3">
                        <?php if ($settings['social_facebook']): ?><a href="<?= $settings['social_facebook'] ?>" class="me-3"><i class="fab fa-facebook fa-lg"></i></a><?php endif; ?>
                        <?php if ($settings['social_twitter']): ?><a href="<?= $settings['social_twitter'] ?>" class="me-3"><i class="fab fa-twitter fa-lg"></i></a><?php endif; ?>
                        <?php if ($settings['social_instagram']): ?><a href="<?= $settings['social_instagram'] ?>" class="me-3"><i class="fab fa-instagram fa-lg"></i></a><?php endif; ?>
                        <?php if ($settings['social_youtube']): ?><a href="<?= $settings['social_youtube'] ?>" class="me-3"><i class="fab fa-youtube fa-lg"></i></a><?php endif; ?>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="tours.php">Tours</a></li>
                        <li><a href="destinations.php">Destinations</a></li>
                        <li><a href="blogs.php">Blog</a></li>
                        <li><a href="gallery.php">Gallery</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Contact Info</h5>
                    <p class="text-muted"><i class="fas fa-envelope me-2"></i><?= htmlspecialchars($settings['contact_email']) ?></p>
                    <p class="text-muted"><i class="fas fa-phone me-2"></i><?= htmlspecialchars($settings['contact_phone']) ?></p>
                    <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($settings['contact_address']) ?></p>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
