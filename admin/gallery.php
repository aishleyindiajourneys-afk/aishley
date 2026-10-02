<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../models/Admin.php';

if (!isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$admin = new Admin($pdo);
$page = $_GET['page'] ?? 1;
$gallery = $admin->getAllGallery($page, 20);
$total = $admin->getGalleryCount();
$pagination = paginate($total, 20, $page);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .btn-primary { background: #FF631E; border: none; }
        .btn-primary:hover { background: #e55a1a; }
        .sidebar { background: linear-gradient(135deg, #FF631E 0%, #201966 100%); min-height: 100vh; color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 12px 20px; border-radius: 8px; margin: 5px 10px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.2); color: white; }
        .sidebar .nav-link i { width: 25px; }
        .main-content { padding: 30px; }
        .page-header { background: white; padding: 20px 30px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .gallery-item { position: relative; overflow: hidden; border-radius: 10px; }
        .gallery-item img { width: 100%; height: 200px; object-fit: cover; transition: transform 0.3s; }
        .gallery-item:hover img { transform: scale(1.1); }
        .gallery-overlay { position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); color: white; padding: 10px; transform: translateY(100%); transition: transform 0.3s; }
        .gallery-item:hover .gallery-overlay { transform: translateY(0); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-4 text-center border-bottom border-white-20"><h4 class="mb-0">Admin Panel</h4></div>
                <nav class="nav flex-column mt-3">
                    <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a class="nav-link" href="tours.php"><i class="fas fa-route"></i> Tours</a>
                    <a class="nav-link" href="destinations.php"><i class="fas fa-map-marker-alt"></i> Destinations</a>
                    <a class="nav-link" href="blogs.php"><i class="fas fa-blog"></i> Blogs</a>
                    <a class="nav-link" href="pages.php"><i class="fas fa-file-alt"></i> Pages</a>
                    <a class="nav-link active" href="gallery.php"><i class="fas fa-images"></i> Gallery</a>
                    <a class="nav-link" href="slider.php"><i class="fas fa-sliders-h"></i> Hero Slider</a>
                    <a class="nav-link" href="testimonials.php"><i class="fas fa-star"></i> Testimonials</a>
                    <a class="nav-link" href="faqs.php"><i class="fas fa-question-circle"></i> FAQs</a>
                    <a class="nav-link" href="enquiries.php"><i class="fas fa-envelope"></i> Enquiries</a>
                    <a class="nav-link" href="media.php"><i class="fas fa-photo-video"></i> Media Library</a>
                    <a class="nav-link" href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                    <a class="nav-link text-danger" href="<?= SITE_URL ?>/controllers/AuthController.php?action=admin-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </div>
            <div class="col-md-9 col-lg-10 main-content">
                <?php if ($flash): ?>
                    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show"><?= $flash['message'] ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Manage Gallery</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-2"></i>Add Image</button>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <?php if ($gallery): ?>
                            <div class="row g-3">
                                <?php foreach ($gallery as $item): ?>
                                    <div class="col-md-4 col-lg-3">
                                        <div class="gallery-item">
                                            <img src="<?= UPLOAD_URL . $item['image'] ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                                            <div class="gallery-overlay">
                                                <small><?= htmlspecialchars($item['title'] ?: 'Untitled') ?></small>
                                                <div class="mt-2">
                                                    <a href="<?= SITE_URL ?>/controllers/AdminController.php?action=gallery-delete&id=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this image?')"><i class="fas fa-trash"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if ($pagination['total_pages'] > 1): ?>
                                <nav class="mt-4"><ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                        <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
                                    <?php endfor; ?>
                                </ul></nav>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted text-center py-4">No images in gallery</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Image to Gallery</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form action="<?= SITE_URL ?>/controllers/AdminController.php?action=gallery-create" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Title</label><input type="text" class="form-control" name="title"></div>
                        <div class="mb-3"><label class="form-label">Image *</label><input type="file" class="form-control" name="image" accept="image/*" required><small class="text-muted">Recommended: 1920x1080px</small></div>
                        <div class="mb-3"><label class="form-label">Category</label><input type="text" class="form-control" name="category"></div>
                        <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="2"></textarea></div>
                        <div class="mb-3"><label class="form-label">Sort Order</label><input type="number" class="form-control" name="sort_order" value="0"></div>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-primary">Add Image</button></div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
