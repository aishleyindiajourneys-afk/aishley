<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../models/Admin.php';

if (!isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$admin = new Admin($pdo);
$id = $_GET['id'] ?? 0;
$destination = $admin->getDestinationById($id);
$flash = getFlash();

if (!$destination) {
    setFlash('error', 'Destination not found');
    redirect(SITE_URL . '/admin/destinations.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Destination - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; color: white; }
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
                    <a class="nav-link active" href="destinations.php"><i class="fas fa-map-marker-alt"></i> Destinations</a>
                    <a class="nav-link" href="blogs.php"><i class="fas fa-blog"></i> Blogs</a>
                    <a class="nav-link" href="pages.php"><i class="fas fa-file-alt"></i> Pages</a>
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
                        <h2 class="mb-0">Edit Destination</h2>
                        <a href="destinations.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
                    </div>
                </div>
                <div class="form-card">
                    <form action="<?= SITE_URL ?>/controllers/AdminController.php?action=destination-update" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $destination['id'] ?>">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3"><label class="form-label">Destination Name *</label><input type="text" class="form-control" name="name" value="<?= htmlspecialchars($destination['name']) ?>" required></div>
                                <div class="row">
                                    <div class="col-md-6 mb-3"><label class="form-label">State *</label><input type="text" class="form-control" name="state" value="<?= htmlspecialchars($destination['state']) ?>" required></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Country</label><input type="text" class="form-control" name="country" value="<?= htmlspecialchars($destination['country']) ?>"></div>
                                </div>
                                <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="5"><?= htmlspecialchars($destination['description']) ?></textarea></div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3"><label class="form-label">Image</label><input type="file" class="form-control" name="image" accept="image/*"><?php if ($destination['image']): ?><img src="<?= UPLOAD_URL.$destination['image'] ?>" class="mt-2" width="100%" style="max-height:150px;object-fit:cover;border-radius:5px;"><?php endif; ?></div>
                                <div class="mb-3"><label class="form-label">Banner Image</label><input type="file" class="form-control" name="banner_image" accept="image/*"><?php if ($destination['banner_image']): ?><img src="<?= UPLOAD_URL.$destination['banner_image'] ?>" class="mt-2" width="100%" style="max-height:150px;object-fit:cover;border-radius:5px;"><?php endif; ?></div>
                                <div class="mb-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="active" <?= $destination['status'] == 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= $destination['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
                                <div class="mb-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="featured" value="yes" id="featured" <?= $destination['featured'] == 'yes' ? 'checked' : '' ?>><label class="form-check-label" for="featured">Featured</label></div></div>
                                <div class="mb-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="popular" value="yes" id="popular" <?= $destination['popular'] == 'yes' ? 'checked' : '' ?>><label class="form-check-label" for="popular">Popular</label></div></div>
                                <hr><h5 class="mb-3">SEO</h5>
                                <div class="mb-3"><label class="form-label">Meta Title</label><input type="text" class="form-control" name="meta_title" value="<?= htmlspecialchars($destination['meta_title']) ?>"></div>
                                <div class="mb-3"><label class="form-label">Meta Description</label><textarea class="form-control" name="meta_description" rows="3"><?= htmlspecialchars($destination['meta_description']) ?></textarea></div>
                            </div>
                        </div>
                        <div class="mt-4"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update</button><a href="destinations.php" class="btn btn-secondary">Cancel</a></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
