<?php
require_once 'config/database.php';
require_once 'config/functions.php';

$settings = getSettings();
$flash = getFlash();

// Get tours with pagination
$page = $_GET['page'] ?? 1;
$perPage = 9;
$offset = ($page - 1) * $perPage;

// Filter by destination
$destinationFilter = $_GET['destination'] ?? '';
$where = "WHERE t.status = 'active'";
$params = [];

if ($destinationFilter) {
    $where .= " AND t.destination_id = ?";
    $params[] = $destinationFilter;
}

// Get total count
$countSql = "SELECT COUNT(*) as count FROM tours t $where";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$total = $stmt->fetch()['count'];

// Get tours
$sql = "SELECT t.*, d.name as destination_name FROM tours t LEFT JOIN destinations d ON t.destination_id = d.id $where ORDER BY t.created_at DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tours = $stmt->fetchAll();

$pagination = paginate($total, $perPage, $page);

// Get destinations for filter
$destinations = $pdo->query("SELECT * FROM destinations WHERE status = 'active' ORDER BY name")->fetchAll();

$pageTitle = "Tour Packages";
$pageDescription = "Explore our amazing tour packages across India. From golden triangle to Kerala backwaters, find your perfect journey.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= generateMetaTags($pageTitle, $pageDescription, 'tour packages, India tours, travel packages') ?>
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
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9)), url('https://images.unsplash.com/photo-1564507592333-c60657eea523?w=1920') center/cover;
            color: white;
            padding: 100px 0;
        }
        .tour-card { background: #fff; height: 100%; display: flex; flex-direction: column; border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; overflow: hidden; }
        .tour-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.2); }
        .tour-card img { width: 100%; height: 250px; object-fit: cover; }
        .tour-card .card-body { display: flex; flex-direction: column; flex: 1; }
        .tour-card-meta { margin-top: auto; }
        .tour-card-actions .btn { white-space: nowrap; }
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
    
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?= SITE_URL ?>"><img src="<?= UPLOAD_URL ?>website/logo.png" alt="<?= htmlspecialchars($settings['site_name']) ?>" style="height: 40px; margin-right: 10px;"><span style="color: #201966; font-weight: 600;"><?= htmlspecialchars($settings['site_name']) ?></span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="tours.php">Tours</a></li>
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
            <h1 class="display-4 fw-bold">Tour Packages</h1>
            <p class="lead">Discover our carefully crafted tour packages across India</p>
        </div>
    </section>
    
    <section class="py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card p-3">
                        <h5 class="mb-3">Filter by Destination</h5>
                        <form method="GET">
                            <select class="form-select" name="destination" onchange="this.form.submit()">
                                <option value="">All Destinations</option>
                                <?php foreach ($destinations as $dest): ?>
                                    <option value="<?= $dest['id'] ?>" <?= $destinationFilter == $dest['id'] ? 'selected' : '' ?>><?= htmlspecialchars($dest['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="col-md-9">
                    <?php if ($tours): ?>
                        <div class="row">
                            <?php foreach ($tours as $tour): ?>
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
                        
                        <!-- Pagination -->
                        <?php if ($pagination['total_pages'] > 1): ?>
                            <nav class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                        <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?><?= $destinationFilter ? '&destination=' . $destinationFilter : '' ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-route fa-4x text-muted mb-3"></i>
                            <h4>No tours found</h4>
                            <p class="text-muted">Try adjusting your filters or check back later.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
