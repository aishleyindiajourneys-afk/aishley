<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../models/Admin.php';

// Check admin authentication
if (!isAdminLoggedIn()) {
    setFlash('error', 'Please login to access admin panel');
    redirect(SITE_URL . '/admin/login.php');
}

$admin = new Admin($pdo);
$stats = $admin->getDashboardStats();
$recentEnquiries = $admin->getRecentEnquiries(5);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .btn-primary { background: #FF631E; border: none; }
        .btn-primary:hover { background: #e55a1a; }
        .sidebar {
            background: linear-gradient(135deg, #FF631E 0%, #201966 100%);
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .sidebar .nav-link i {
            width: 25px;
        }
        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .main-content {
            padding: 30px;
        }
        .page-header {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-4 text-center border-bottom border-white-20">
                    <img src="<?= UPLOAD_URL ?>website/logo.png" alt="Aishley India Journeys" class="mb-2" style="height: 40px; margin-right: 10px;">
                    <h4 class="mb-0">Admin Panel</h4>
                </div>
                <nav class="nav flex-column mt-3">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link" href="tours.php">
                        <i class="fas fa-route"></i> Tours
                    </a>
                    <a class="nav-link" href="destinations.php">
                        <i class="fas fa-map-marker-alt"></i> Destinations
                    </a>
                    <a class="nav-link" href="blogs.php">
                        <i class="fas fa-blog"></i> Blogs
                    </a>
                    <a class="nav-link" href="pages.php">
                        <i class="fas fa-file-alt"></i> Pages
                    </a>
                    <a class="nav-link" href="gallery.php">
                        <i class="fas fa-images"></i> Gallery
                    </a>
                    <a class="nav-link" href="testimonials.php">
                        <i class="fas fa-star"></i> Testimonials
                    </a>
                    <a class="nav-link" href="faqs.php">
                        <i class="fas fa-question-circle"></i> FAQs
                    </a>
                    <a class="nav-link" href="enquiries.php">
                        <i class="fas fa-envelope"></i> Enquiries
                        <?php if ($stats['new_enquiries'] > 0): ?>
                            <span class="badge bg-danger ms-auto"><?= $stats['new_enquiries'] ?></span>
                        <?php endif; ?>
                    </a>
                    <a class="nav-link" href="media.php">
                        <i class="fas fa-photo-video"></i> Media Library
                    </a>
                    <a class="nav-link" href="settings.php">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                    <a class="nav-link text-danger" href="<?= SITE_URL ?>/controllers/AuthController.php?action=admin-logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <?php if ($flash): ?>
                    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
                        <?= $flash['message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">Dashboard</h2>
                            <p class="text-muted mb-0">Welcome back, <?= $_SESSION['admin_name'] ?></p>
                        </div>
                        <a href="<?= SITE_URL ?>" target="_blank" class="btn btn-outline-primary">
                            <i class="fas fa-external-link-alt me-2"></i>View Website
                        </a>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row g-4 mb-5">
                    <div class="col-md-6 col-lg-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                                        <i class="fas fa-route"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0"><?= $stats['total_tours'] ?></h3>
                                        <p class="text-muted mb-0">Total Tours</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0"><?= $stats['total_destinations'] ?></h3>
                                        <p class="text-muted mb-0">Destinations</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                                        <i class="fas fa-blog"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0"><?= $stats['total_blogs'] ?></h3>
                                        <p class="text-muted mb-0">Blog Posts</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0"><?= $stats['total_enquiries'] ?></h3>
                                        <p class="text-muted mb-0">Enquiries</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Enquiries -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Recent Enquiries</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($recentEnquiries): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Tour</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentEnquiries as $enquiry): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($enquiry['name']) ?></td>
                                                <td><?= htmlspecialchars($enquiry['email']) ?></td>
                                                <td><?= $enquiry['tour_title'] ? htmlspecialchars($enquiry['tour_title']) : 'General' ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $enquiry['status'] == 'new' ? 'danger' : ($enquiry['status'] == 'contacted' ? 'warning' : 'success') ?>">
                                                        <?= ucfirst($enquiry['status']) ?>
                                                    </span>
                                                </td>
                                                <td><?= formatDate($enquiry['created_at']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center py-4">No enquiries yet</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
