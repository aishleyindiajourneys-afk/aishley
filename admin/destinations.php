<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../models/Admin.php';

if (!isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$admin = new Admin($pdo);
$page = $_GET['page'] ?? 1;
$destinations = $admin->getAllDestinations($page, 10);
$total = $admin->getDestinationCount();
$pagination = paginate($total, 10, $page);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Destinations - Admin Panel</title>
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
                        <h2 class="mb-0">Manage Destinations</h2>
                        <a href="destinations-create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Destination</a>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <?php if ($destinations): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead><tr><th>Image</th><th>Name</th><th>State</th><th>Featured</th><th>Popular</th><th>Status</th><th>Actions</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($destinations as $dest): ?>
                                            <tr>
                                                <td><?= $dest['image'] ? '<img src="'.UPLOAD_URL.$dest['image'].'" width="60" height="40" style="object-fit:cover;border-radius:5px;">' : '-' ?></td>
                                                <td><?= htmlspecialchars($dest['name']) ?></td>
                                                <td><?= htmlspecialchars($dest['state']) ?></td>
                                                <td><?= $dest['featured'] == 'yes' ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-muted"></i>' ?></td>
                                                <td><?= $dest['popular'] == 'yes' ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-muted"></i>' ?></td>
                                                <td><span class="badge bg-<?= $dest['status'] == 'active' ? 'success' : 'danger' ?>"><?= ucfirst($dest['status']) ?></span></td>
                                                <td>
                                                    <a href="destinations-edit.php?id=<?= $dest['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                                    <a href="<?= SITE_URL ?>/controllers/AdminController.php?action=destination-delete&id=<?= $dest['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if ($pagination['total_pages'] > 1): ?>
                                <nav class="mt-4"><ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                        <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
                                    <?php endfor; ?>
                                </ul></nav>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted text-center py-4">No destinations found. <a href="destinations-create.php">Add your first destination</a></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
