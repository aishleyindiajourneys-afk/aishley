<?php
require_once 'config/database.php';
require_once 'config/functions.php';

$settings = getSettings();
$flash = getFlash();

// Get testimonials
$stmt = $pdo->query("SELECT t.*, tour.title as tour_title FROM testimonials t LEFT JOIN tours tour ON t.tour_id = tour.id WHERE t.status = 'active' ORDER BY t.created_at DESC");
$testimonials = $stmt->fetchAll();

$pageTitle = "Testimonials";
$pageDescription = "Read what our travelers say about their experiences with Aishley India Journeys.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= generateMetaTags($pageTitle, $pageDescription, 'testimonials, reviews, traveler experiences') ?>
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
        .testimonial-card { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); padding: 30px; }
        .testimonial-card img { width: 80px; height: 80px; object-fit: cover; border-radius: 50%; }
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
            <h1 class="display-4 fw-bold">What Our Travelers Say</h1>
            <p class="lead">Real experiences from real travelers</p>
        </div>
    </section>
    
    <section class="py-5">
        <div class="container">
            <?php if ($testimonials): ?>
                <div class="row">
                    <?php foreach ($testimonials as $t): ?>
                        <div class="col-md-4 mb-4">
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center mb-3">
                                    <?php if ($t['image']): ?>
                                        <img src="<?= UPLOAD_URL . $t['image'] ?>" alt="<?= htmlspecialchars($t['name']) ?>">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;"><?= strtoupper(substr($t['name'], 0, 1)) ?></div>
                                    <?php endif; ?>
                                    <div class="ms-3">
                                        <h6 class="mb-0"><?= htmlspecialchars($t['name']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($t['location']) ?></small>
                                    </div>
                                </div>
                                <div class="mb-3"><?= str_repeat('<i class="fas fa-star text-warning"></i>', $t['rating']) ?></div>
                                <p class="text-muted">"<?= htmlspecialchars($t['review']) ?>"</p>
                                <?php if ($t['tour_title']): ?>
                                    <small class="text-primary"><i class="fas fa-route me-1"></i><?= htmlspecialchars($t['tour_title']) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-star fa-4x text-muted mb-3"></i>
                    <h4>No testimonials yet</h4>
                    <p class="text-muted">Be the first to share your experience!</p>
                </div>
            <?php endif; ?>
            
            <div class="text-center mt-5">
                <h4 class="mb-3">Share Your Experience</h4>
                <p class="text-muted mb-4">Traveled with us? We'd love to hear from you!</p>
                <a href="contact.php" class="btn btn-primary">Submit Your Testimonial</a>
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
