<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../models/Admin.php';

if (!isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$admin = new Admin($pdo);
$settings = $admin->getSettings();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
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
                    <a class="nav-link" href="pages.php"><i class="fas fa-file-alt"></i> Pages</a>
                    <a class="nav-link" href="gallery.php"><i class="fas fa-images"></i> Gallery</a>
                    <a class="nav-link" href="slider.php"><i class="fas fa-sliders-h"></i> Hero Slider</a>
                    <a class="nav-link" href="testimonials.php"><i class="fas fa-star"></i> Testimonials</a>
                    <a class="nav-link" href="faqs.php"><i class="fas fa-question-circle"></i> FAQs</a>
                    <a class="nav-link" href="enquiries.php"><i class="fas fa-envelope"></i> Enquiries</a>
                    <a class="nav-link" href="media.php"><i class="fas fa-photo-video"></i> Media Library</a>
                    <a class="nav-link active" href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                    <a class="nav-link text-danger" href="<?= SITE_URL ?>/controllers/AuthController.php?action=admin-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </div>
            <div class="col-md-9 col-lg-10 main-content">
                <?php if ($flash): ?>
                    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show"><?= $flash['message'] ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>
                <div class="page-header">
                    <h2 class="mb-0">Website Settings</h2>
                </div>
                <div class="form-card">
                    <form action="<?= SITE_URL ?>/controllers/AdminController.php?action=settings-update" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $settings['id'] ?>">
                        <h5 class="mb-3">General Settings</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Site Name</label><input type="text" class="form-control" name="site_name" value="<?= htmlspecialchars($settings['site_name']) ?>"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Site Tagline</label><input type="text" class="form-control" name="site_tagline" value="<?= htmlspecialchars($settings['site_tagline']) ?>"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3"><label class="form-label">Site Logo</label><input type="file" class="form-control" name="site_logo" accept="image/*"><?php if ($settings['site_logo']): ?><img src="<?= UPLOAD_URL.$settings['site_logo'] ?>" class="mt-2" height="40"><?php endif; ?><small class="text-muted">Recommended: 200x50px (PNG/SVG)</small></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Site Favicon</label><input type="file" class="form-control" name="site_favicon" accept="image/*"><?php if ($settings['site_favicon']): ?><img src="<?= UPLOAD_URL.$settings['site_favicon'] ?>" class="mt-2" height="40"><?php endif; ?><small class="text-muted">Recommended: 32x32px or 16x16px (ICO/PNG)</small></div>
                        </div>
                        <hr>
                        <h5 class="mb-3">Contact Information</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3"><label class="form-label">Contact Email</label><input type="email" class="form-control" name="contact_email" value="<?= htmlspecialchars($settings['contact_email']) ?>"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Contact Phone</label><input type="text" class="form-control" name="contact_phone" value="<?= htmlspecialchars($settings['contact_phone']) ?>"></div>
                        </div>
                        <div class="mb-3"><label class="form-label">Contact Address</label><textarea class="form-control" name="contact_address" rows="3"><?= htmlspecialchars($settings['contact_address']) ?></textarea></div>
                        <hr>
                        <h5 class="mb-3">Social Media</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Facebook</label><input type="url" class="form-control" name="social_facebook" value="<?= htmlspecialchars($settings['social_facebook']) ?>"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Twitter</label><input type="url" class="form-control" name="social_twitter" value="<?= htmlspecialchars($settings['social_twitter']) ?>"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Instagram</label><input type="url" class="form-control" name="social_instagram" value="<?= htmlspecialchars($settings['social_instagram']) ?>"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">LinkedIn</label><input type="url" class="form-control" name="social_linkedin" value="<?= htmlspecialchars($settings['social_linkedin']) ?>"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">YouTube</label><input type="url" class="form-control" name="social_youtube" value="<?= htmlspecialchars($settings['social_youtube']) ?>"></div>
                        </div>
                        <hr>
                        <h5 class="mb-3">SEO Settings</h5>
                        <div class="mb-3"><label class="form-label">SEO Keywords</label><textarea class="form-control" name="seo_keywords" rows="2"><?= htmlspecialchars($settings['seo_keywords']) ?></textarea></div>
                        <div class="mb-3"><label class="form-label">SEO Description</label><textarea class="form-control" name="seo_description" rows="3"><?= htmlspecialchars($settings['seo_description']) ?></textarea></div>
                        <div class="mb-3"><label class="form-label">Google Analytics</label><textarea class="form-control" name="google_analytics" rows="5" placeholder="Paste your Google Analytics tracking code here"><?= htmlspecialchars($settings['google_analytics']) ?></textarea></div>
                        <div class="mt-4"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Settings</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
