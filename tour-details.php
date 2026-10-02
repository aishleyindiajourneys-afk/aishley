<?php
require_once 'config/database.php';
require_once 'config/functions.php';
require_once 'models/Auth.php';

$settings = getSettings();
$flash = getFlash();
$slug = $_GET['slug'] ?? '';

// Get tour details
$stmt = $pdo->prepare("SELECT t.*, d.name as destination_name, d.state FROM tours t LEFT JOIN destinations d ON t.destination_id = d.id WHERE t.slug = ? AND t.status = 'active'");
$stmt->execute([$slug]);
$tour = $stmt->fetch();

if (!$tour) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Check if favorite
$isFavorite = false;
if (isLoggedIn()) {
    $auth = new Auth($pdo);
    $isFavorite = $auth->isFavorite($_SESSION['user_id'], $tour['id']);
}

// Get related tours
$stmt = $pdo->prepare("SELECT * FROM tours WHERE destination_id = ? AND id != ? AND status = 'active' ORDER BY created_at DESC LIMIT 4");
$stmt->execute([$tour['destination_id'], $tour['id']]);
$relatedTours = $stmt->fetchAll();

$pageTitle = $tour['title'];
$pageDescription = $tour['meta_description'] ?: truncate($tour['overview'], 160);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= generateMetaTags($pageTitle, $pageDescription, $tour['meta_title'] ?? '') ?>
    <?php if ($tour['featured_image']): ?>
        <meta property="og:image" content="<?= UPLOAD_URL . $tour['featured_image'] ?>">
    <?php endif; ?>
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
        .hero-image { height: 400px; object-fit: cover; border-radius: 15px; }
        .info-card { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .related-card { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); overflow: hidden; }
        .related-card img { height: 180px; object-fit: cover; }
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
    
    <div class="container mt-5 pt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="tours.php">Tours</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($tour['title']) ?></li>
            </ol>
        </nav>
        
        <div class="row mt-4">
            <div class="col-lg-8">
                <?php if ($tour['featured_image']): ?>
                    <img src="<?= UPLOAD_URL . $tour['featured_image'] ?>" alt="<?= htmlspecialchars($tour['title']) ?>" class="hero-image w-100 mb-4">
                <?php endif; ?>
                
                <h1 class="mb-3"><?= htmlspecialchars($tour['title']) ?></h1>
                <div class="d-flex align-items-center mb-4">
                    <span class="badge bg-primary me-3"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($tour['destination_name']) ?></span>
                    <span class="text-muted"><i class="fas fa-clock me-1"></i><?= htmlspecialchars($tour['duration']) ?></span>
                </div>
                
                <div class="info-card p-4 mb-4">
                    <h4>Overview</h4>
                    <p><?= nl2br(htmlspecialchars($tour['overview'])) ?></p>
                </div>
                
                <?php if ($tour['highlights']): ?>
                    <div class="info-card p-4 mb-4">
                        <h4>Highlights</h4>
                        <ul>
                            <?php foreach (explode("\n", $tour['highlights']) as $highlight): ?>
                                <?php if (trim($highlight)): ?>
                                    <li><?= htmlspecialchars(trim($highlight)) ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if ($tour['itinerary']): ?>
                    <div class="info-card p-4 mb-4">
                        <h4>Itinerary</h4>
                        <div><?= $tour['itinerary'] ?></div>
                    </div>
                <?php endif; ?>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="info-card p-4">
                            <h5 class="text-success"><i class="fas fa-check-circle me-2"></i>Inclusions</h5>
                            <ul class="small">
                                <?php foreach (explode("\n", $tour['inclusions']) as $item): ?>
                                    <?php if (trim($item)): ?>
                                        <li><?= htmlspecialchars(trim($item)) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card p-4">
                            <h5 class="text-danger"><i class="fas fa-times-circle me-2"></i>Exclusions</h5>
                            <ul class="small">
                                <?php foreach (explode("\n", $tour['exclusions']) as $item): ?>
                                    <?php if (trim($item)): ?>
                                        <li><?= htmlspecialchars(trim($item)) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <?php if ($relatedTours): ?>
                    <h4 class="mb-3">Related Tours</h4>
                    <div class="row">
                        <?php foreach ($relatedTours as $rt): ?>
                            <div class="col-md-6 mb-3">
                                <div class="related-card">
                                    <?php if ($rt['featured_image']): ?>
                                        <img src="<?= UPLOAD_URL . $rt['featured_image'] ?>" alt="<?= htmlspecialchars($rt['title']) ?>">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;"><i class="fas fa-image fa-2x text-muted"></i></div>
                                    <?php endif; ?>
                                    <div class="card-body p-3">
                                        <h6 class="card-title"><?= htmlspecialchars($rt['title']) ?></h6>
                                        <p class="text-muted small mb-2"><?= htmlspecialchars($rt['duration']) ?></p>
                                        <a href="tour-details.php?slug=<?= $rt['slug'] ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-lg-4">
                <div class="info-card p-4 mb-4 sticky-top" style="top: 100px;">
                    <h4 class="mb-3">Book This Tour</h4>
                    <div class="mb-3">
                        <?php if ($tour['discount_price']): ?>
                            <span class="text-decoration-line-through text-muted small me-2"><?= formatPrice($tour['price']) ?></span>
                            <span class="display-6 fw-bold text-primary"><?= formatPrice($tour['discount_price']) ?></span>
                            <span class="badge bg-danger ms-2">Save <?= round(($tour['price'] - $tour['discount_price']) / $tour['price'] * 100) ?>%</span>
                        <?php else: ?>
                            <span class="display-6 fw-bold text-primary"><?= formatPrice($tour['price']) ?></span>
                        <?php endif; ?>
                        <p class="text-muted small">per person</p>
                    </div>
                    
                    <?php if (isLoggedIn()): ?>
                        <button class="btn btn-outline-danger w-100 mb-2 add-fav" data-tour-id="<?= $tour['id'] ?>" <?= $isFavorite ? 'disabled' : '' ?>>
                            <i class="fas fa-heart me-2"></i><?= $isFavorite ? 'Already in Favorites' : 'Add to Favorites' ?>
                        </button>
                    <?php endif; ?>
                    
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['contact_phone'] ?? '') ?>?text=<?= urlencode('Hi, I am interested in the tour: ' . $tour['title']) ?>" target="_blank" class="btn btn-success w-100 mb-2">
                        <i class="fab fa-whatsapp me-2"></i>WhatsApp
                    </a>
                    
                    <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#enquiryModal">
                        <i class="fas fa-envelope me-2"></i>Send Enquiry
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="enquiryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Send Enquiry</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form action="controllers/EnquiryController.php?action=create" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="tour_id" value="<?= $tour['id'] ?>">
                        <div class="mb-3"><label class="form-label">Name *</label><input type="text" class="form-control" name="name" required></div>
                        <div class="mb-3"><label class="form-label">Email *</label><input type="email" class="form-control" name="email" required></div>
                        <div class="mb-3"><label class="form-label">Phone *</label><input type="text" class="form-control" name="phone" required></div>
                        <div class="mb-3"><label class="form-label">Subject</label><input type="text" class="form-control" name="subject" value="Enquiry for: <?= htmlspecialchars($tour['title']) ?>"></div>
                        <div class="mb-3"><label class="form-label">Message *</label><textarea class="form-control" name="message" rows="4" required></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-primary">Send Enquiry</button></div>
                </form>
            </div>
        </div>
    </div>
    
    <footer class="footer mt-5">
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
    <script>
        document.querySelector('.add-fav')?.addEventListener('click', function() {
            const tourId = this.dataset.tourId;
            fetch('<?= SITE_URL ?>/controllers/AuthController.php?action=add-favorite', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'tour_id=' + tourId
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    this.innerHTML = '<i class="fas fa-heart me-2"></i>Added to Favorites';
                    this.disabled = true;
                }
            });
        });
    </script>
</body>
</html>
