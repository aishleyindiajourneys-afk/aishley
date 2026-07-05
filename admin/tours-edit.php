<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../models/Admin.php';

if (!isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$admin = new Admin($pdo);
$id = $_GET['id'] ?? 0;
$tour = $admin->getTourById($id);
$destinations = $pdo->query("SELECT * FROM destinations WHERE status = 'active' ORDER BY name")->fetchAll();
$flash = getFlash();

if (!$tour) {
    setFlash('error', 'Tour not found');
    redirect(SITE_URL . '/admin/tours.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tour - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 10px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .sidebar .nav-link i { width: 25px; }
        .main-content { padding: 30px; }
        .page-header {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .form-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 30px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-4 text-center border-bottom border-white-20">
                    <h4 class="mb-0">Admin Panel</h4>
                </div>
                <nav class="nav flex-column mt-3">
                    <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a class="nav-link active" href="tours.php"><i class="fas fa-route"></i> Tours</a>
                    <a class="nav-link" href="destinations.php"><i class="fas fa-map-marker-alt"></i> Destinations</a>
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
                        <h2 class="mb-0">Edit Tour</h2>
                        <a href="tours.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Tours</a>
                    </div>
                </div>
                
                <div class="form-card">
                    <form action="<?= SITE_URL ?>/controllers/AdminController.php?action=tour-update" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $tour['id'] ?>">
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Tour Title *</label>
                                    <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($tour['title']) ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Destination</label>
                                    <select class="form-select" name="destination_id">
                                        <option value="">Select Destination</option>
                                        <?php foreach ($destinations as $dest): ?>
                                            <option value="<?= $dest['id'] ?>" <?= $tour['destination_id'] == $dest['id'] ? 'selected' : '' ?>><?= htmlspecialchars($dest['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Duration *</label>
                                        <input type="text" class="form-control" name="duration" value="<?= htmlspecialchars($tour['duration']) ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Price (₹) *</label>
                                        <input type="number" class="form-control" name="price" value="<?= $tour['price'] ?>" step="0.01" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Discount Price (₹)</label>
                                    <input type="number" class="form-control" name="discount_price" value="<?= $tour['discount_price'] ?>" step="0.01">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Overview</label>
                                    <textarea class="form-control" name="overview" rows="4"><?= htmlspecialchars($tour['overview']) ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Highlights</label>
                                    <textarea class="form-control" name="highlights" rows="3" placeholder="One highlight per line"><?= htmlspecialchars($tour['highlights']) ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Itinerary</label>
                                    <textarea class="form-control" name="itinerary" rows="6" id="itinerary"><?= htmlspecialchars($tour['itinerary']) ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Inclusions</label>
                                    <textarea class="form-control" name="inclusions" rows="3" placeholder="One item per line"><?= htmlspecialchars($tour['inclusions']) ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Exclusions</label>
                                    <textarea class="form-control" name="exclusions" rows="3" placeholder="One item per line"><?= htmlspecialchars($tour['exclusions']) ?></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Featured Image</label>
                                    <input type="file" class="form-control" name="featured_image" accept="image/*">
                                    <?php if ($tour['featured_image']): ?>
                                        <img src="<?= UPLOAD_URL . $tour['featured_image'] ?>" alt="Current Image" class="mt-2" width="100%" style="max-height: 200px; object-fit: cover; border-radius: 5px;">
                                    <?php endif; ?>
                                    <small class="text-muted">Recommended: 1200x600px</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status">
                                        <option value="active" <?= $tour['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= $tour['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="featured" value="yes" id="featured" <?= $tour['featured'] == 'yes' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="featured">Featured Tour</label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="popular" value="yes" id="popular" <?= $tour['popular'] == 'yes' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="popular">Popular Tour</label>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <h5 class="mb-3">SEO Settings</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label">Meta Title</label>
                                    <input type="text" class="form-control" name="meta_title" value="<?= htmlspecialchars($tour['meta_title']) ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Meta Description</label>
                                    <textarea class="form-control" name="meta_description" rows="3"><?= htmlspecialchars($tour['meta_description']) ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Tour</button>
                            <a href="tours.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#itinerary'))
            .catch(error => {
                console.error(error);
            });
    </script>
</body>
</html>
