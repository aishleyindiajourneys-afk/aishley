<?php
require_once 'config/database.php';
require_once 'config/functions.php';

$settings = getSettings();
$flash = getFlash();
$slug = $_GET['slug'] ?? '';

// Get blog details
$stmt = $pdo->prepare("SELECT b.*, c.name as category_name FROM blogs b LEFT JOIN blog_categories c ON b.category_id = c.id WHERE b.slug = ? AND b.status = 'published'");
$stmt->execute([$slug]);
$blog = $stmt->fetch();

if (!$blog) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Get related blogs
$stmt = $pdo->prepare("SELECT * FROM blogs WHERE category_id = ? AND id != ? AND status = 'published' ORDER BY created_at DESC LIMIT 4");
$stmt->execute([$blog['category_id'], $blog['id']]);
$relatedBlogs = $stmt->fetchAll();

$pageTitle = $blog['title'];
$pageDescription = $blog['meta_description'] ?: truncate($blog['excerpt'], 160);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= generateMetaTags($pageTitle, $pageDescription, $blog['meta_title'] ?? '') ?>
    <?php if ($blog['featured_image']): ?>
        <meta property="og:image" content="<?= UPLOAD_URL . $blog['featured_image'] ?>">
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
        .blog-content { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); padding: 40px; }
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
                    <li class="nav-item"><a class="nav-link active" href="blogs.php">Blog</a></li>
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
                <li class="breadcrumb-item"><a href="blogs.php">Blog</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($blog['title']) ?></li>
            </ol>
        </nav>
        
        <div class="row mt-4">
            <div class="col-lg-8">
                <div class="blog-content">
                    <?php if ($blog['featured_image']): ?>
                        <img src="<?= UPLOAD_URL . $blog['featured_image'] ?>" alt="<?= htmlspecialchars($blog['title']) ?>" class="w-100 mb-4" style="border-radius: 10px; max-height: 500px; object-fit: cover;">
                    <?php endif; ?>
                    
                    <h1 class="mb-3"><?= htmlspecialchars($blog['title']) ?></h1>
                    <div class="d-flex align-items-center mb-4 text-muted">
                        <span class="me-3"><i class="fas fa-folder me-1"></i><?= htmlspecialchars($blog['category_name']) ?></span>
                        <span class="me-3"><i class="fas fa-user me-1"></i><?= htmlspecialchars($blog['author']) ?></span>
                        <span><i class="fas fa-calendar me-1"></i><?= formatDate($blog['created_at']) ?></span>
                    </div>
                    
                    <div class="blog-content-body">
                        <?= $blog['content'] ?>
                    </div>
                </div>
                
                <?php if ($relatedBlogs): ?>
                    <h4 class="mt-5 mb-3">Related Articles</h4>
                    <div class="row">
                        <?php foreach ($relatedBlogs as $rb): ?>
                            <div class="col-md-6 mb-3">
                                <div class="related-card">
                                    <?php if ($rb['featured_image']): ?>
                                        <img src="<?= UPLOAD_URL . $rb['featured_image'] ?>" alt="<?= htmlspecialchars($rb['title']) ?>">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;"><i class="fas fa-image fa-2x text-muted"></i></div>
                                    <?php endif; ?>
                                    <div class="card-body p-3">
                                        <h6 class="card-title"><?= htmlspecialchars($rb['title']) ?></h6>
                                        <a href="blog-details.php?slug=<?= $rb['slug'] ?>" class="btn btn-sm btn-outline-primary">Read More</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-lg-4">
                <div class="card p-4 mb-4">
                    <h5>Categories</h5>
                    <ul class="list-unstyled">
                        <?php $cats = $pdo->query("SELECT * FROM blog_categories WHERE status = 'active'")->fetchAll(); ?>
                        <?php foreach ($cats as $cat): ?>
                            <li><a href="blogs.php?category=<?= $cat['id'] ?>" class="text-decoration-none text-muted"><?= htmlspecialchars($cat['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
