<?php
require_once 'config/database.php';
require_once 'config/functions.php';

$settings = getSettings();
$flash = getFlash();

// Get featured tours
$featuredTours = $pdo->query("SELECT t.*, d.name as destination_name FROM tours t LEFT JOIN destinations d ON t.destination_id = d.id WHERE t.featured = 'yes' AND t.status = 'active' ORDER BY t.created_at DESC LIMIT 6")->fetchAll();

// Get popular destinations
$popularDestinations = $pdo->query("SELECT * FROM destinations WHERE popular = 'yes' AND status = 'active' ORDER BY created_at DESC LIMIT 6")->fetchAll();
$enquiryDestinations = $pdo->query("SELECT id, name FROM destinations WHERE status = 'active' ORDER BY name ASC")->fetchAll();

// Get testimonials
$testimonials = $pdo->query("SELECT * FROM testimonials WHERE featured = 'yes' AND status = 'active' ORDER BY created_at DESC LIMIT 6")->fetchAll();

// Get latest blogs
$latestBlogs = $pdo->query("SELECT b.*, c.name as category_name FROM blogs b LEFT JOIN blog_categories c ON b.category_id = c.id WHERE b.status = 'published' ORDER BY b.created_at DESC LIMIT 3")->fetchAll();

$pageTitle = "Home";
$pageDescription = $settings['seo_description'] ?? "Experience the magic of India with Aishley India Journeys. Discover amazing tours, destinations, and create unforgettable memories.";

$heroText = "Experience unforgettable journeys through ancient temples, majestic forts, serene backwaters, and vibrant culture with Aishley India Journeys.";
$heroSlides = [];
try {
    $adminSlides = $pdo->query("SELECT * FROM hero_slides WHERE status = 'active' ORDER BY sort_order ASC, id ASC")->fetchAll();
    foreach ($adminSlides as $slide) {
        $heroSlides[] = [
            'src' => UPLOAD_URL . str_replace(' ', '%20', $slide['image']),
            'title' => $slide['title'],
            'text' => $slide['subtitle'] ?: $heroText,
            'button_text' => $slide['button_text'] ?: 'Explore Tours',
            'button_link' => $slide['button_link'] ?: 'tours.php'
        ];
    }
} catch (PDOException $e) {
    $heroSlides = [];
}
if (!$heroSlides) {
    $heroSlides[] = [
        'src' => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?auto=format&fit=crop&w=1920&q=80',
        'title' => 'Discover the Magic of India',
        'text' => $heroText,
        'button_text' => 'Explore Tours',
        'button_link' => 'tours.php'
    ];
}
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
        :root { --primary: #FF631E; --secondary: #201966; --light-bg: #EAE9E7; --white: #FEFEFE; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .hero-section { position: relative; color: white; }
        .hero-section .carousel,
        .hero-section .carousel-inner,
        .hero-section .carousel-item { min-height: 80vh; }
        .hero-slide-img { width: 100%; height: 80vh; object-fit: cover; }
        .hero-overlay { position: absolute; inset: 0; background: linear-gradient(135deg, rgba(0,0,0,0.55), rgba(0,0,0,0.35)); }
        .hero-caption {
            position: absolute;
            inset: 0;
            z-index: 2;
            display: flex;
            align-items: center;
            text-align: left;
        }
        .hero-section .carousel-control-prev,
        .hero-section .carousel-control-next { width: 6%; z-index: 3; }
        .hero-section .carousel-indicators { z-index: 3; margin-bottom: 1.5rem; }
        .site-header { position: sticky; top: 0; z-index: 1030; }
        .navbar { background: #FEFEFE !important; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-brand img { height: 40px; }
        .nav-link { color: #333 !important; font-weight: 500; }
        .nav-link:hover { color: var(--primary) !important; }
        .btn-primary { background: #FF631E; border: none; }
        .btn-primary:hover { background: #e55a1a; }
        .section-title { position: relative; margin-bottom: 40px; }
        .section-title::after { content: ''; position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); width: 60px; height: 3px; background: linear-gradient(135deg, var(--primary), var(--secondary)); }
        .tour-card { background: #fff; height: 100%; display: flex; flex-direction: column; border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; overflow: hidden; }
        .tour-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.2); }
        .tour-card img { width: 100%; height: 250px; object-fit: cover; }
        .tour-card .card-body { display: flex; flex-direction: column; flex: 1; }
        .tour-card-meta { margin-top: auto; }
        .tour-card-actions .btn { white-space: nowrap; }
        .destination-card { border-radius: 15px; overflow: hidden; position: relative; }
        .destination-card img { height: 300px; object-fit: cover; transition: transform 0.5s; }
        .destination-card:hover img { transform: scale(1.1); }
        .destination-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); display: flex; align-items: flex-end; padding: 20px; }
        .testimonial-card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .cta-section { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; padding: 80px 0; }
        .footer { background: #201966; color: #FEFEFE; padding: 60px 0 30px; }
        .footer h5 { color: #FEFEFE; font-weight: 600; margin-bottom: 20px; }
        .footer a { color: #EAE9E7; text-decoration: none; }
        .footer a:hover { color: #FF631E; }
        .footer .text-muted { color: #EAE9E7 !important; }
        .footer p { color: #EAE9E7; }
        .footer i { color: #EAE9E7; }
        
        /* World Map Background for white sections */
        .world-map-section {
            position: relative;
            background-image: url('uploads/website/bg-1.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .world-map-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.92);
            pointer-events: none;
        }
        .world-map-section > * {
            position: relative;
            z-index: 1;
        }
        
        /* AI Chatbot */
        .chatbot-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #FF631E, #201966);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            z-index: 9999;
            transition: transform 0.3s;
        }
        .chatbot-button:hover {
            transform: scale(1.1);
        }
        .chatbot-button i {
            color: white;
            font-size: 28px;
        }
        .chatbot-window {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 350px;
            height: 450px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.3);
            z-index: 9999;
            display: none;
            flex-direction: column;
            overflow: hidden;
        }
        .chatbot-window.active {
            display: flex;
        }
        .chatbot-header {
            background: linear-gradient(135deg, #FF631E, #201966);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .chatbot-header h5 {
            margin: 0;
            font-size: 16px;
        }
        .chatbot-header .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
        }
        .chatbot-messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background: #f5f5f5;
        }
        .chatbot-message {
            margin-bottom: 10px;
            max-width: 80%;
        }
        .chatbot-message.bot {
            background: #e3f2fd;
            padding: 10px 15px;
            border-radius: 15px 15px 15px 0;
            align-self: flex-start;
        }
        .chatbot-message.user {
            background: #FF631E;
            color: white;
            padding: 10px 15px;
            border-radius: 15px 15px 0 15px;
            margin-left: auto;
        }
        .chatbot-input {
            padding: 15px;
            border-top: 1px solid #ddd;
            display: flex;
            gap: 10px;
        }
        .chatbot-input input {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 25px;
            padding: 10px 15px;
            outline: none;
        }
        .chatbot-input button {
            background: #FF631E;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
        }
        
        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, #FF631E, #201966);
            color: white;
            padding: 10px 48px 10px 16px;
            text-align: center;
            position: relative;
        }
        .welcome-banner p {
            margin: 0;
            font-size: 14px;
        }
        .welcome-banner a {
            color: white;
            text-decoration: underline;
            font-weight: 600;
        }
        .welcome-banner .close-banner {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            opacity: 0.8;
        }
        .welcome-banner .close-banner:hover {
            opacity: 1;
        }
        #leadPopup { z-index: 10050; }
        .modal-backdrop { z-index: 10040 !important; }
        .lead-popup-dialog { max-width: 520px; }
        .lead-popup-header {
            background: linear-gradient(135deg, #FF631E, #201966);
            color: #fff;
            border: none;
            padding: 22px 24px 18px;
        }
        .lead-popup-header .btn-close { filter: invert(1); }
        .lead-popup-header h5 { font-weight: 700; margin-bottom: 4px; }
        .lead-popup-header p { margin: 0; opacity: 0.9; font-size: 14px; }
        .lead-popup-body { padding: 24px; }
        body.modal-open .chatbot-button,
        body.modal-open .chatbot-window { z-index: 1; }
    </style>
</head>
<body>
    <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x" style="z-index: 9999;"><?= $flash['message'] ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    
    <header class="site-header">
        <div class="welcome-banner" id="welcomeBanner">
            <div class="container">
                <p>Welcome to Aishley India Journey, talk with our travel experts for instant quote, you can leave your inquiry on <a href="https://wa.me/919876543210" target="_blank">WhatsApp</a> & Call <a href="tel:+919876543210">+91 9876543210</a></p>
            </div>
            <button class="close-banner" onclick="closeWelcomeBanner()">&times;</button>
        </div>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand" href="<?= SITE_URL ?>"><img src="<?= UPLOAD_URL ?>website/logo.png" alt="<?= htmlspecialchars($settings['site_name']) ?>" style="height: 40px; margin-right: 10px;"><span style="color: #201966; font-weight: 600;"><?= htmlspecialchars($settings['site_name']) ?></span></a>
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
    </header>
    
    <section class="hero-section">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5500">
            <?php if (count($heroSlides) > 1): ?>
                <div class="carousel-indicators">
                    <?php foreach ($heroSlides as $i => $slide): ?>
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>" <?= $i === 0 ? 'aria-current="true"' : '' ?> aria-label="Slide <?= $i + 1 ?>"></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="carousel-inner">
                <?php foreach ($heroSlides as $i => $slide): ?>
                    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                        <img src="<?= htmlspecialchars($slide['src']) ?>" class="hero-slide-img" alt="<?= htmlspecialchars($slide['title']) ?>">
                        <div class="hero-overlay"></div>
                        <div class="hero-caption">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <h1 class="display-4 fw-bold mb-4"><?= htmlspecialchars($slide['title']) ?></h1>
                                        <p class="lead mb-4"><?= htmlspecialchars($slide['text']) ?></p>
                                        <a href="<?= htmlspecialchars($slide['button_link'] ?? 'tours.php') ?>" class="btn btn-light btn-lg me-3"><?= htmlspecialchars($slide['button_text'] ?? 'Explore Tours') ?></a>
                                        <a href="contact.php" class="btn btn-outline-light btn-lg">Plan Your Trip</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($heroSlides) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            <?php endif; ?>
        </div>
    </section>
    
    <section class="py-5 world-map-section">
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
                                    <div class="tour-card-meta mt-3">
                                        <div class="tour-card-price mb-2">
                                            <?php if ($tour['discount_price']): ?>
                                                <span class="text-decoration-line-through text-muted small d-block"><?= formatPrice($tour['price']) ?></span>
                                                <span class="fw-bold text-primary"><?= formatPrice($tour['discount_price']) ?></span>
                                            <?php else: ?>
                                                <span class="fw-bold text-primary"><?= formatPrice($tour['price']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="tour-card-actions d-flex gap-2">
                                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['contact_phone'] ?? '') ?>?text=<?= urlencode('Hi, I am interested in the tour: ' . $tour['title']) ?>" target="_blank" class="btn btn-sm btn-success flex-fill"><i class="fab fa-whatsapp me-1"></i>WhatsApp</a>
                                            <a href="tour-details.php?slug=<?= $tour['slug'] ?>" class="btn btn-sm btn-outline-primary flex-fill">View Details</a>
                                        </div>
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
                            <a href="tours.php?destination=<?= (int)$dest['id'] ?>" class="text-decoration-none">
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
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-center mt-4"><a href="destinations.php" class="btn btn-primary">Explore All Destinations</a></div>
            <?php else: ?>
                <p class="text-center text-muted">No destinations available.</p>
            <?php endif; ?>
        </div>
    </section>
    
    <section class="py-5 world-map-section">
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
    
    <section class="py-5 world-map-section">
        <div class="container">
            <h2 class="text-center section-title">Our Partner Hotels</h2>
            <div id="hotelCarousel" class="carousel slide mt-4" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&h=300&fit=crop" class="card-img-top" alt="Hotel 1" style="height: 200px; object-fit: cover;">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Taj Palace</h6>
                                        <p class="text-muted small">New Delhi</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <img src="https://images.unsplash.com/photo-1582719508461-905c673771fd?w=400&h=300&fit=crop" class="card-img-top" alt="Hotel 2" style="height: 200px; object-fit: cover;">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Oberoi Hotels</h6>
                                        <p class="text-muted small">Mumbai</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=400&h=300&fit=crop" class="card-img-top" alt="Hotel 3" style="height: 200px; object-fit: cover;">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">ITC Grand</h6>
                                        <p class="text-muted small">Bangalore</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=400&h=300&fit=crop" class="card-img-top" alt="Hotel 4" style="height: 200px; object-fit: cover;">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Leela Palace</h6>
                                        <p class="text-muted small">Chennai</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=400&h=300&fit=crop" class="card-img-top" alt="Hotel 5" style="height: 200px; object-fit: cover;">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Marriott</h6>
                                        <p class="text-muted small">Jaipur</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <img src="https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=400&h=300&fit=crop" class="card-img-top" alt="Hotel 6" style="height: 200px; object-fit: cover;">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Hyatt Regency</h6>
                                        <p class="text-muted small">Kolkata</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <img src="https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=400&h=300&fit=crop" class="card-img-top" alt="Hotel 7" style="height: 200px; object-fit: cover;">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Radisson Blu</h6>
                                        <p class="text-muted small">Goa</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <img src="https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=400&h=300&fit=crop" class="card-img-top" alt="Hotel 8" style="height: 200px; object-fit: cover;">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Hilton</h6>
                                        <p class="text-muted small">Hyderabad</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#hotelCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon bg-dark rounded-circle" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#hotelCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon bg-dark rounded-circle" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
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
    
    <?php include 'includes/footer.php'; ?>
    
    <div class="modal fade" id="leadPopup" tabindex="-1" aria-labelledby="leadPopupTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered lead-popup-dialog">
            <div class="modal-content border-0 overflow-hidden" style="border-radius: 16px;">
                <div class="modal-header lead-popup-header">
                    <div>
                        <h5 class="modal-title" id="leadPopupTitle">Plan Your India Trip</h5>
                        <p>Share your details and our travel expert will send you a custom quote.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body lead-popup-body">
                    <div id="leadPopupAlert" class="alert d-none mb-3" role="alert"></div>
                    <form id="leadPopupForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone *</label>
                                <input type="tel" class="form-control" name="phone" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Preferred destination</label>
                                <select class="form-select" name="destination">
                                    <option value="">Select destination</option>
                                    <?php foreach ($enquiryDestinations as $destination): ?>
                                        <option value="<?= htmlspecialchars($destination['name']) ?>"><?= htmlspecialchars($destination['name']) ?></option>
                                    <?php endforeach; ?>
                                    <option value="Not sure / Multiple">Not sure / Multiple</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Travel month</label>
                                <input type="text" class="form-control" name="travel_dates" placeholder="e.g. December 2026">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="Tell us about your trip"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" id="leadPopupSubmit">
                            <i class="fas fa-paper-plane me-2"></i>Get a Free Quote
                        </button>
                        <a class="btn btn-success w-100 mt-2" href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['contact_phone'] ?? '919876543210') ?>?text=<?= urlencode('Hi, I want a custom India tour quote.') ?>" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>Chat on WhatsApp
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- AI Chatbot -->
    <div class="chatbot-button" onclick="toggleChatbot()">
        <i class="fas fa-headset"></i>
    </div>
    <div class="chatbot-window" id="chatbotWindow">
        <div class="chatbot-header">
            <h5><i class="fas fa-headset me-2"></i>Customer Support</h5>
            <button class="close-btn" onclick="toggleChatbot()">&times;</button>
        </div>
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="chatbot-message bot">
                Hello! I'm here to help you. Please provide some information so our agent can connect with you soon.
            </div>
        </div>
        <div class="chatbot-input" id="chatbotInputContainer">
            <input type="text" id="chatbotInput" placeholder="Type your answer..." onkeypress="handleKeyPress(event)">
            <button onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function closeWelcomeBanner() {
            document.getElementById('welcomeBanner').style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const leadModalEl = document.getElementById('leadPopup');
            if (!leadModalEl || sessionStorage.getItem('aishley_lead_popup_shown')) {
                return;
            }
            const leadModal = new bootstrap.Modal(leadModalEl);
            setTimeout(function() {
                leadModal.show();
                sessionStorage.setItem('aishley_lead_popup_shown', '1');
            }, 1200);

            const form = document.getElementById('leadPopupForm');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = document.getElementById('leadPopupSubmit');
                const alertBox = document.getElementById('leadPopupAlert');
                const data = {
                    name: form.name.value.trim(),
                    email: form.email.value.trim(),
                    phone: form.phone.value.trim(),
                    subject: 'Homepage Lead',
                    message: form.notes.value.trim() || ('Homepage lead enquiry. Destination: ' + (form.destination.value.trim() || 'Not specified') + '. Travel month: ' + (form.travel_dates.value.trim() || 'Flexible') + '.')
                };
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
                fetch('controllers/EnquiryController.php?action=create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams(data)
                })
                .then(r => r.json())
                .then(function(res) {
                    alertBox.classList.remove('d-none', 'alert-danger', 'alert-success');
                    if (res.success) {
                        alertBox.classList.add('alert-success');
                        alertBox.textContent = 'Thank you! Our travel expert will contact you soon.';
                        form.reset();
                        setTimeout(function() { leadModal.hide(); }, 1800);
                    } else {
                        alertBox.classList.add('alert-danger');
                        alertBox.textContent = res.message || 'Could not send your request. Please try again.';
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Get a Free Quote';
                })
                .catch(function() {
                    alertBox.classList.remove('d-none', 'alert-success');
                    alertBox.classList.add('alert-danger');
                    alertBox.textContent = 'Could not send your request. Please try again.';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Get a Free Quote';
                });
            });
        });
        
        let currentStep = 0;
        let enquiryData = {
            name: '',
            email: '',
            phone: '',
            message: ''
        };
        
        const questions = [
            { field: 'name', question: 'What is your name?', placeholder: 'Enter your name' },
            { field: 'email', question: 'What is your email address?', placeholder: 'Enter your email' },
            { field: 'phone', question: 'What is your phone number?', placeholder: 'Enter your phone number' },
            { field: 'message', question: 'How can we help you?', placeholder: 'Describe your query' }
        ];
        
        function toggleChatbot() {
            const chatbotWindow = document.getElementById('chatbotWindow');
            chatbotWindow.classList.toggle('active');
            if (chatbotWindow.classList.contains('active') && currentStep === 0) {
                showQuestion();
            }
        }
        
        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        }
        
        function showQuestion() {
            const messagesContainer = document.getElementById('chatbotMessages');
            const input = document.getElementById('chatbotInput');
            
            if (currentStep < questions.length) {
                const botMessage = document.createElement('div');
                botMessage.className = 'chatbot-message bot';
                botMessage.textContent = questions[currentStep].question;
                messagesContainer.appendChild(botMessage);
                input.placeholder = questions[currentStep].placeholder;
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }
        
        function sendMessage() {
            const input = document.getElementById('chatbotInput');
            const message = input.value.trim();
            if (!message) return;
            
            const messagesContainer = document.getElementById('chatbotMessages');
            
            // Add user message
            const userMessage = document.createElement('div');
            userMessage.className = 'chatbot-message user';
            userMessage.textContent = message;
            messagesContainer.appendChild(userMessage);
            
            // Store answer
            if (currentStep < questions.length) {
                enquiryData[questions[currentStep].field] = message;
                currentStep++;
                input.value = '';
                
                // Scroll to bottom
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                
                // Show next question or submit
                setTimeout(() => {
                    if (currentStep < questions.length) {
                        showQuestion();
                    } else {
                        submitEnquiry();
                    }
                }, 500);
            }
        }
        
        function submitEnquiry() {
            const messagesContainer = document.getElementById('chatbotMessages');
            const inputContainer = document.getElementById('chatbotInputContainer');
            
            // Show processing message
            const botMessage = document.createElement('div');
            botMessage.className = 'chatbot-message bot';
            botMessage.textContent = 'Submitting your enquiry...';
            messagesContainer.appendChild(botMessage);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            // Submit to server
            fetch('controllers/EnquiryController.php?action=create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams(enquiryData)
            })
            .then(response => response.json())
            .then(data => {
                const finalMessage = document.createElement('div');
                finalMessage.className = 'chatbot-message bot';
                finalMessage.textContent = 'Thank you! Our agent will connect with you soon.';
                messagesContainer.appendChild(finalMessage);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                
                // Hide input
                inputContainer.style.display = 'none';
                
                // Reset after 3 seconds
                setTimeout(() => {
                    currentStep = 0;
                    enquiryData = { name: '', email: '', phone: '', message: '' };
                    messagesContainer.innerHTML = '<div class="chatbot-message bot">Hello! I\'m here to help you. Please provide some information so our agent can connect with you soon.</div>';
                    inputContainer.style.display = 'flex';
                    showQuestion();
                }, 3000);
            })
            .catch(error => {
                const errorMessage = document.createElement('div');
                errorMessage.className = 'chatbot-message bot';
                errorMessage.textContent = 'Sorry, there was an error. Please try again or contact us directly.';
                messagesContainer.appendChild(errorMessage);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            });
        }
    </script>
</body>
</html>
