<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../models/Admin.php';

if (!isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$admin = new Admin($pdo);
$page = $_GET['page'] ?? 1;
$enquiries = $admin->getAllEnquiries($page, 20);
$total = $admin->getEnquiryCount();
$pagination = paginate($total, 20, $page);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Enquiries - Admin Panel</title>
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
                    <a class="nav-link" href="testimonials.php"><i class="fas fa-star"></i> Testimonials</a>
                    <a class="nav-link" href="faqs.php"><i class="fas fa-question-circle"></i> FAQs</a>
                    <a class="nav-link active" href="enquiries.php"><i class="fas fa-envelope"></i> Enquiries</a>
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
                    <h2 class="mb-0">Manage Enquiries</h2>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <?php if ($enquiries): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Tour</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($enquiries as $e): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($e['name']) ?></td>
                                                <td><?= htmlspecialchars($e['email']) ?></td>
                                                <td><?= htmlspecialchars($e['phone']) ?></td>
                                                <td><?= $e['tour_title'] ?: 'General' ?></td>
                                                <td><span class="badge bg-<?= $e['status'] == 'new' ? 'danger' : ($e['status'] == 'contacted' ? 'warning' : 'success') ?>"><?= ucfirst($e['status']) ?></span></td>
                                                <td><?= formatDate($e['created_at']) ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewModal<?= $e['id'] ?>"><i class="fas fa-eye"></i></button>
                                                    <form action="<?= SITE_URL ?>/controllers/AdminController.php?action=enquiry-update" method="POST" class="d-inline">
                                                        <input type="hidden" name="id" value="<?= $e['id'] ?>">
                                                        <input type="hidden" name="status" value="<?= $e['status'] == 'new' ? 'contacted' : 'closed' ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Update status?')"><i class="fas fa-check"></i></button>
                                                    </form>
                                                    <a href="<?= SITE_URL ?>/controllers/AdminController.php?action=enquiry-delete&id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
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
                            <p class="text-muted text-center py-4">No enquiries found</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php foreach ($enquiries as $e): ?>
        <div class="modal fade" id="viewModal<?= $e['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Enquiry Details</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <p><strong>Name:</strong> <?= htmlspecialchars($e['name']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($e['email']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($e['phone']) ?></p>
                        <p><strong>Tour:</strong> <?= $e['tour_title'] ?: 'General' ?></p>
                        <p><strong>Subject:</strong> <?= htmlspecialchars($e['subject']) ?></p>
                        <p><strong>Message:</strong></p>
                        <p><?= nl2br(htmlspecialchars($e['message'])) ?></p>
                        <p><strong>Date:</strong> <?= formatDate($e['created_at'], 'd M Y H:i') ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
