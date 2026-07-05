<?php
require_once 'config/database.php';
require_once 'config/functions.php';

$settings = getSettings();
$flash = getFlash();

// Get FAQs
$stmt = $pdo->query("SELECT * FROM faqs WHERE status = 'active' ORDER BY sort_order ASC, created_at DESC");
$faqs = $stmt->fetchAll();

$pageTitle = "FAQs";
$pageDescription = "Frequently asked questions about our tours and services. Find answers to common queries.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= generateMetaTags($pageTitle, $pageDescription, 'FAQ, frequently asked questions, tour queries') ?>
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
        .faq-card { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); padding: 30px; }
        .accordion-item { border: none; border-radius: 10px !important; margin-bottom: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .accordion-button { border-radius: 10px !important; font-weight: 600; }
        .accordion-button:not(.collapsed) { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; }
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
            <h1 class="display-4 fw-bold">Frequently Asked Questions</h1>
            <p class="lead">Find answers to common questions about our tours</p>
        </div>
    </section>
    
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="faq-card">
                        <?php if ($faqs): ?>
                            <div class="accordion" id="faqAccordion">
                                <?php foreach ($faqs as $index => $faq): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading<?= $index ?>">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>">
                                                <?= htmlspecialchars($faq['question']) ?>
                                            </button>
                                        </h2>
                                        <div id="collapse<?= $index ?>" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                            <div class="accordion-body">
                                                <?= nl2br(htmlspecialchars($faq['answer'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-question-circle fa-4x text-muted mb-3"></i>
                                <h4>No FAQs available</h4>
                                <p class="text-muted">Check back later or contact us with your questions.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="text-center mt-4">
                        <p class="text-muted">Can't find your answer?</p>
                        <a href="contact.php" class="btn btn-primary">Contact Us</a>
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
