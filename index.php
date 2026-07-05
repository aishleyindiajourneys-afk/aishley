<?php
require_once 'config/database.php';
require_once 'config/functions.php';

$settings = getSettings();
$flash = getFlash();

// Get featured tours
$featuredTours = $pdo->query("SELECT t.*, d.name as destination_name FROM tours t LEFT JOIN destinations d ON t.destination_id = d.id WHERE t.featured = 'yes' AND t.status = 'active' ORDER BY t.created_at DESC LIMIT 6")->fetchAll();

// Get popular destinations
$popularDestinations = $pdo->query("SELECT * FROM destinations WHERE popular = 'yes' AND status = 'active' ORDER BY created_at DESC LIMIT 6")->fetchAll();

// Get testimonials
$testimonials = $pdo->query("SELECT * FROM testimonials WHERE featured = 'yes' AND status = 'active' ORDER BY created_at DESC LIMIT 6")->fetchAll();

// Get latest blogs
$latestBlogs = $pdo->query("SELECT b.*, c.name as category_name FROM blogs b LEFT JOIN blog_categories c ON b.category_id = c.id WHERE b.status = 'published' ORDER BY b.created_at DESC LIMIT 3")->fetchAll();

$pageTitle = "Home";
$pageDescription = $settings['seo_description'] ?? "Experience the magic of India with Aishley India Journeys. Discover amazing tours, destinations, and create unforgettable memories.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= generateMetaTags($pageTitle, $pageDescription, 'India tourism, tours, travel, destinations') ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #667eea; --secondary: #764ba2; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .hero-section {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9)), url('https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=1920') center/cover;
            min-height: 80vh;
            display: flex;
            align-items: center;
            color: white;
        }
        .navbar { background: rgba(255,255,255,0.95) !important; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: 700; color: var(--primary) !important; }
        .nav-link { color: #333 !important; font-weight: 500; }
        .nav-link:hover { color: var(--primary) !important; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--secondary)); border: none; }
        .btn-primary:hover { background: linear-gradient(135deg, var(--secondary), var(--primary)); }
        .section-title { position: relative; margin-bottom: 40px; }
        .section-title::after { content: ''; position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); width: 60px; height: 3px; background: linear-gradient(135deg, var(--primary), var(--secondary)); }
        .tour-card { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; overflow: hidden; }
        .tour-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.2); }
        .tour-card img { height: 250px; object-fit: cover; }
        .destination-card { border-radius: 15px; overflow: hidden; position: relative; }
        .destination-card img { height: 300px; object-fit: cover; transition: transform 0.5s; }
        .destination-card:hover img { transform: scale(1.1); }
        .destination-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); display: flex; align-items: flex-end; padding: 20px; }
        .testimonial-card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .cta-section { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; padding: 80px 0; }
        .footer { background: #1a1a2e; color: white; padding: 60px 0 30px; }
        .footer a { color: #aaa; text-decoration: none; }
        .footer a:hover { color: white; }
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
                    <li class="nav-item"><a class="nav-link active" href="<?= SITE_URL ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="tours.php">Tours</a></li>
                    <li class="nav-item"><a class="nav-link" href="destinations.php">Destinations</a></li>
                    <li class="nav-item"><a class="nav-link" href="blogs.php">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="gallery.php">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item"><a class="nav-link" href="user/dashboard.php"><i class="fas fa-user me-1"></i>My Account</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a></li>
                        <li class="nav-item"><a class="nav-link btn btn-primary text-white ms-2 px-4" href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">Discover the Magic of India</h1>
                    <p class="lead mb-4">Experience unforgettable journeys through ancient temples, majestic forts, serene backwaters, and vibrant culture with Aishley India Journeys.</p>
                    <a href="tours.php" class="btn btn-light btn-lg me-3">Explore Tours</a>
                    <a href="contact.php" class="btn btn-outline-light btn-lg">Plan Your Trip</a>
                </div>
            </div>
        </div>
    </section>
    
    <section class="py-5">
        <div class="container">
            <h2 class="text-center section-title">Featured Tours</h2>
            <?php if ($featuredTours): ?>
                <div class="row">
                    <?php foreach ($featuredTours as $tour): ?>
                        <div class="col-md-4 mb-4">
                            <div class="tour-card">
                                <?php if ($tour['featured_image']): ?>
                                    <img src="<?= UPLOAD_URL . $tour['featured_image'] ?>" alt="<?= htmlspecialchars($tour['title']) ?>">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 250px;"><i class="fas fa-image fa-3x text-muted"></i></div>
                                <?php endif; ?>
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-primary"><?= htmlspecialchars($tour['destination_name']) ?></span>
                                        <small class="text-muted"><i class="fas fa-clock me-1"></i><?= htmlspecialchars($tour['duration']) ?></small>
                                    </div>
                                    <h5 class="card-title"><?= htmlspecialchars($tour['title']) ?></h5>
                                    <p class="card-text text-muted small"><?= truncate($tour['overview'], 80) ?></p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>
                                            <?php if ($tour['discount_price']): ?>
                                                <span class="text-decoration-line-through text-muted small me-2"><?= formatPrice($tour['price']) ?></span>
                                                <span class="fw-bold text-primary"><?= formatPrice($tour['discount_price']) ?></span>
                                            <?php else: ?>
                                                <span class="fw-bold text-primary"><?= formatPrice($tour['price']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <a href="tour-details.php?slug=<?= $tour['slug'] ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-center mt-4"><a href="tours.php" class="btn btn-primary">View All Tours</a></div>
            <?php else: ?>
                <p class="text-center text-muted">No featured tours available.</p>
            <?php endif; ?>
        </div>
    </section>
    
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center section-title">Popular Destinations</h2>
            <?php if ($popularDestinations): ?>
                <div class="row">
                    <?php foreach ($popularDestinations as $dest): ?>
                        <div class="col-md-4 mb-4">
                            <div class="destination-card">
                                <?php if ($dest['image']): ?>
                                    <img src="<?= UPLOAD_URL . $dest['image'] ?>" alt="<?= htmlspecialchars($dest['name']) ?>">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 300px;"><i class="fas fa-map-marker-alt fa-3x text-muted"></i></div>
                                <?php endif; ?>
                                <div class="destination-overlay">
                                    <div class="text-white">
                                        <h5><?= htmlspecialchars($dest['name']) ?></h5>
                                        <p class="small mb-0"><i class="fas fa-map-pin me-1"></i><?= htmlspecialchars($dest['state']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-center mt-4"><a href="destinations.php" class="btn btn-primary">Explore All Destinations</a></div>
            <?php else: ?>
                <p class="text-center text-muted">No destinations available.</p>
            <?php endif; ?>
        </div>
    </section>
    
    <section class="py-5">
        <div class="container">
            <h2 class="text-center section-title">Why Choose Us</h2>
            <div class="row mt-4">
                <div class="col-md-4 text-center mb-4">
                    <div class="p-4">
                        <i class="fas fa-award fa-3x text-primary mb-3"></i>
                        <h5>Expert Guides</h5>
                        <p class="text-muted">Experienced local guides who know every hidden gem</p>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="p-4">
                        <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                        <h5>Safe & Secure</h5>
                        <p class="text-muted">Your safety is our top priority throughout the journey</p>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="p-4">
                        <i class="fas fa-dollar-sign fa-3x text-primary mb-3"></i>
                        <h5>Best Prices</h5>
                        <p class="text-muted">Competitive pricing with no hidden charges</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center section-title">What Our Travelers Say</h2>
            <?php if ($testimonials): ?>
                <div class="row mt-4">
                    <?php foreach ($testimonials as $t): ?>
                        <div class="col-md-4 mb-4">
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center mb-3">
                                    <?php if ($t['image']): ?>
                                        <img src="<?= UPLOAD_URL . $t['image'] ?>" alt="<?= htmlspecialchars($t['name']) ?>" class="rounded-circle me-3" width="50" height="50" style="object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;"><?= strtoupper(substr($t['name'], 0, 1)) ?></div>
                                    <?php endif; ?>
                                    <div>
                                        <h6 class="mb-0"><?= htmlspecialchars($t['name']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($t['location']) ?></small>
                                    </div>
                                </div>
                                <div class="mb-3"><?= str_repeat('<i class="fas fa-star text-warning"></i>', $t['rating']) ?></div>
                                <p class="text-muted">"<?= htmlspecialchars(truncate($t['review'], 150)) ?>"</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-muted">No testimonials yet.</p>
            <?php endif; ?>
        </div>
    </section>
    
    <section class="py-5">
        <div class="container">
            <h2 class="text-center section-title">Latest from Our Blog</h2>
            <?php if ($latestBlogs): ?>
                <div class="row mt-4">
                    <?php foreach ($latestBlogs as $blog): ?>
                        <div class="col-md-4 mb-4">
                            <div class="tour-card">
                                <?php if ($blog['featured_image']): ?>
                                    <img src="<?= UPLOAD_URL . $blog['featured_image'] ?>" alt="<?= htmlspecialchars($blog['title']) ?>">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;"><i class="fas fa-image fa-3x text-muted"></i></div>
                                <?php endif; ?>
                                <div class="card-body p-4">
                                    <small class="text-muted"><i class="fas fa-folder me-1"></i><?= htmlspecialchars($blog['category_name']) ?></small>
                                    <h5 class="card-title mt-2"><?= htmlspecialchars($blog['title']) ?></h5>
                                    <p class="card-text text-muted small"><?= truncate($blog['excerpt'], 100) ?></p>
                                    <a href="blog-details.php?slug=<?= $blog['slug'] ?>" class="btn btn-sm btn-outline-primary">Read More</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-muted">No blog posts yet.</p>
            <?php endif; ?>
        </div>
    </section>
    
    <section class="cta-section text-center">
        <div class="container">
            <h2 class="mb-4">Ready to Start Your Journey?</h2>
            <p class="lead mb-4">Let us help you plan the perfect trip to India</p>
            <a href="contact.php" class="btn btn-light btn-lg">Contact Us Now</a>
        </div>
    </section>
    
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center section-title">Subscribe to Our Newsletter</h2>
            <div class="row justify-content-center mt-4">
                <div class="col-md-6">
                    <form class="d-flex gap-2">
                        <input type="email" class="form-control" placeholder="Enter your email" required>
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </form>
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
