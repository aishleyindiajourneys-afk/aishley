<?php
require_once 'config/database.php';
require_once 'config/functions.php';
require_once 'models/Admin.php';

$settings = getSettings();
$flash = getFlash();
$slug = $_GET['slug'] ?? '';

$admin = new Admin($pdo);
$page = $admin->getPageBySlug($slug);

if (!$page) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

$pageTitle = $page['title'];
$pageDescription = $page['meta_description'] ?: truncate(strip_tags($page['content']), 160);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= generateMetaTags($pageTitle, $pageDescription, $page['meta_title'] ?? '') ?>
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
        .page-content { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); padding: 40px; }
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
                <li class="breadcrumb-item active"><?= htmlspecialchars($page['title']) ?></li>
            </ol>
        </nav>
        
        <div class="page-content mt-4">
            <h1 class="mb-4"><?= htmlspecialchars($page['title']) ?></h1>
            <?php if ($page['featured_image']): ?>
                <img src="<?= UPLOAD_URL . $page['featured_image'] ?>" alt="<?= htmlspecialchars($page['title']) ?>" class="w-100 mb-4" style="border-radius: 10px; max-height: 400px; object-fit: cover;">
            <?php endif; ?>
            <div class="page-body">
                <?= $page['content'] ?>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
