<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../models/Admin.php';

if (!isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$admin = new Admin($pdo);
$slides = $admin->getAllHeroSlides();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hero Slider - Admin Panel</title>
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
        .slide-thumb { width: 160px; height: 90px; object-fit: cover; border-radius: 8px; }
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
                    <a class="nav-link" href="gallery.php"><i class="fas fa-images"></i> Gallery</a>
                    <a class="nav-link active" href="slider.php"><i class="fas fa-sliders-h"></i> Hero Slider</a>
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
                        <div>
                            <h2 class="mb-1">Hero Slider</h2>
                            <p class="text-muted mb-0">Images shown in the homepage banner. Recommended size: 1920x1080px.</p>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-2"></i>Add Slide</button>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <?php if ($slides): ?>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Order</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($slides as $slide): ?>
                                            <tr>
                                                <td><img src="<?= UPLOAD_URL . $slide['image'] ?>" alt="" class="slide-thumb"></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($slide['title']) ?></strong>
                                                    <div class="text-muted small"><?= htmlspecialchars(truncate($slide['subtitle'] ?? '', 80)) ?></div>
                                                </td>
                                                <td><?= (int)$slide['sort_order'] ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $slide['status'] === 'active' ? 'success' : 'secondary' ?>"><?= htmlspecialchars($slide['status']) ?></span>
                                                </td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $slide['id'] ?>"><i class="fas fa-edit"></i></button>
                                                    <a href="<?= SITE_URL ?>/controllers/AdminController.php?action=slider-delete&id=<?= $slide['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this slider image?')"><i class="fas fa-trash"></i></a>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="editModal<?= $slide['id'] ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header"><h5 class="modal-title">Edit Slide</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                                                        <form action="<?= SITE_URL ?>/controllers/AdminController.php?action=slider-update" method="POST" enctype="multipart/form-data">
                                                            <input type="hidden" name="id" value="<?= $slide['id'] ?>">
                                                            <div class="modal-body">
                                                                <div class="mb-3"><label class="form-label">Title *</label><input type="text" class="form-control" name="title" value="<?= htmlspecialchars($slide['title']) ?>" required></div>
                                                                <div class="mb-3"><label class="form-label">Subtitle</label><textarea class="form-control" name="subtitle" rows="3"><?= htmlspecialchars($slide['subtitle'] ?? '') ?></textarea></div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Image</label>
                                                                    <input type="file" class="form-control" name="image" accept="image/*">
                                                                    <small class="text-muted">Leave empty to keep the current image. Recommended: 1920x1080px</small>
                                                                    <div class="mt-2"><img src="<?= UPLOAD_URL . $slide['image'] ?>" alt="" class="slide-thumb"></div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3"><label class="form-label">Button Text</label><input type="text" class="form-control" name="button_text" value="<?= htmlspecialchars($slide['button_text']) ?>"></div>
                                                                    <div class="col-md-6 mb-3"><label class="form-label">Button Link</label><input type="text" class="form-control" name="button_link" value="<?= htmlspecialchars($slide['button_link']) ?>"></div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3"><label class="form-label">Sort Order</label><input type="number" class="form-control" name="sort_order" value="<?= (int)$slide['sort_order'] ?>"></div>
                                                                    <div class="col-md-6 mb-3"><label class="form-label">Status</label>
                                                                        <select class="form-select" name="status">
                                                                            <option value="active" <?= $slide['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                                            <option value="inactive" <?= $slide['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer"><button type="submit" class="btn btn-primary">Save Changes</button></div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center py-4">No slider images yet. Add one to show on the homepage hero.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Slider Image</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form action="<?= SITE_URL ?>/controllers/AdminController.php?action=slider-create" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Title *</label><input type="text" class="form-control" name="title" required></div>
                        <div class="mb-3"><label class="form-label">Subtitle</label><textarea class="form-control" name="subtitle" rows="3"></textarea></div>
                        <div class="mb-3"><label class="form-label">Image *</label><input type="file" class="form-control" name="image" accept="image/*" required><small class="text-muted">Recommended: 1920x1080px</small></div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Button Text</label><input type="text" class="form-control" name="button_text" value="Explore Tours"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Button Link</label><input type="text" class="form-control" name="button_link" value="tours.php"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Sort Order</label><input type="number" class="form-control" name="sort_order" value="0"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-primary">Add Slide</button></div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
