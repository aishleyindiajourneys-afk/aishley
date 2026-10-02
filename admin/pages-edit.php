<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../models/Admin.php';

if (!isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$admin = new Admin($pdo);
$id = $_GET['id'] ?? 0;
$page = $admin->getPageById($id);
$flash = getFlash();

if (!$page) {
    setFlash('error', 'Page not found');
    redirect(SITE_URL . '/admin/pages.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Page - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
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
        .form-card { background: white; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 30px; }
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
                    <a class="nav-link active" href="pages.php"><i class="fas fa-file-alt"></i> Pages</a>
                    <a class="nav-link" href="gallery.php"><i class="fas fa-images"></i> Gallery</a>
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
                        <h2 class="mb-0">Edit Page</h2>
                        <a href="pages.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
                    </div>
                </div>
                <div class="form-card">
                    <form action="<?= SITE_URL ?>/controllers/AdminController.php?action=page-update" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $page['id'] ?>">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3"><label class="form-label">Page Title *</label><input type="text" class="form-control" name="title" value="<?= htmlspecialchars($page['title']) ?>" required></div>
                                <div class="mb-3"><label class="form-label">Content</label><textarea class="form-control" name="content" rows="15" id="content"><?= htmlspecialchars($page['content']) ?></textarea></div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3"><label class="form-label">Featured Image</label><input type="file" class="form-control" name="featured_image" accept="image/*"><?php if ($page['featured_image']): ?><img src="<?= UPLOAD_URL.$page['featured_image'] ?>" class="mt-2" width="100%" style="max-height:150px;object-fit:cover;border-radius:5px;"><?php endif; ?></div>
                                <div class="mb-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="active" <?= $page['status'] == 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= $page['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
                                <hr><h5 class="mb-3">SEO</h5>
                                <div class="mb-3"><label class="form-label">Meta Title</label><input type="text" class="form-control" name="meta_title" value="<?= htmlspecialchars($page['meta_title']) ?>"></div>
                                <div class="mb-3"><label class="form-label">Meta Description</label><textarea class="form-control" name="meta_description" rows="3"><?= htmlspecialchars($page['meta_description']) ?></textarea></div>
                            </div>
                        </div>
                        <div class="mt-4"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update</button><a href="pages.php" class="btn btn-secondary">Cancel</a></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>ClassicEditor.create(document.querySelector('#content')).catch(error => console.error(error));</script>
</body>
</html>
