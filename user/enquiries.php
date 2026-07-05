<?php
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../models/Auth.php';

if (!isLoggedIn()) {
    setFlash('error', 'Please login to access your account');
    redirect(SITE_URL . '/login.php');
}

$auth = new Auth($pdo);
$user = $auth->getUserById($_SESSION['user_id']);
$enquiries = $auth->getUserEnquiries($_SESSION['user_id']);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Enquiries - Aishley India Journeys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .user-sidebar { background: white; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .user-sidebar .nav-link { color: #333; padding: 12px 20px; border-radius: 8px; margin: 5px 10px; }
        .user-sidebar .nav-link:hover, .user-sidebar .nav-link.active { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .user-sidebar .nav-link i { width: 25px; }
        .main-content { padding: 30px; }
        .page-header { background: white; padding: 20px 30px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= SITE_URL ?>">Aishley India Journeys</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/tours.php">Tours</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/destinations.php">Destinations</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">My Account</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= SITE_URL ?>/controllers/AuthController.php?action=user-logout">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="user-sidebar p-4">
                    <div class="text-center mb-4">
                        <?php if ($user['profile_image']): ?>
                            <img src="<?= UPLOAD_URL . $user['profile_image'] ?>" alt="Profile" class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; margin: 0 auto; font-size: 32px;"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
                        <?php endif; ?>
                        <h5 class="mt-3"><?= htmlspecialchars($user['name']) ?></h5>
                        <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <a class="nav-link" href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                        <a class="nav-link" href="favorites.php"><i class="fas fa-heart"></i> Favorite Tours</a>
                        <a class="nav-link active" href="enquiries.php"><i class="fas fa-envelope"></i> My Enquiries</a>
                        <a class="nav-link text-danger" href="<?= SITE_URL ?>/controllers/AuthController.php?action=user-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </nav>
                </div>
            </div>
            <div class="col-md-9">
                <?php if ($flash): ?>
                    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show"><?= $flash['message'] ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>
                
                <div class="page-header">
                    <h2 class="mb-0">My Enquiries</h2>
                    <p class="text-muted mb-0"><?= count($enquiries) ?> enquiries sent</p>
                </div>
                
                <?php if ($enquiries): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tour</th>
                                            <th>Subject</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($enquiries as $e): ?>
                                            <tr>
                                                <td><?= $e['tour_title'] ?: 'General Enquiry' ?></td>
                                                <td><?= htmlspecialchars($e['subject']) ?: '-' ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $e['status'] == 'new' ? 'danger' : ($e['status'] == 'contacted' ? 'warning' : 'success') ?>">
                                                        <?= ucfirst($e['status']) ?>
                                                    </span>
                                                </td>
                                                <td><?= formatDate($e['created_at']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-envelope fa-4x text-muted mb-3"></i>
                        <h4>No enquiries yet</h4>
                        <p class="text-muted">Have questions? Send us an enquiry about any tour!</p>
                        <a href="<?= SITE_URL ?>/tours.php" class="btn btn-primary">Browse Tours</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
