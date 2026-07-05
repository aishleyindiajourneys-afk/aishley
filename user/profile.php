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
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Aishley India Journeys</title>
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
        .form-card { background: white; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 30px; }
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
                        <a class="nav-link active" href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                        <a class="nav-link" href="favorites.php"><i class="fas fa-heart"></i> Favorite Tours</a>
                        <a class="nav-link" href="enquiries.php"><i class="fas fa-envelope"></i> My Enquiries</a>
                        <a class="nav-link text-danger" href="<?= SITE_URL ?>/controllers/AuthController.php?action=user-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </nav>
                </div>
            </div>
            <div class="col-md-9">
                <?php if ($flash): ?>
                    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show"><?= $flash['message'] ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>
                
                <div class="page-header">
                    <h2 class="mb-0">My Profile</h2>
                    <p class="text-muted mb-0">Update your personal information</p>
                </div>
                
                <div class="form-card">
                    <h5 class="mb-4">Personal Information</h5>
                    <form action="<?= SITE_URL ?>/controllers/AuthController.php?action=update-profile" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Profile Image</label>
                                <input type="file" class="form-control" name="profile_image" accept="image/*">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="3"><?= htmlspecialchars($user['address']) ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Profile</button>
                    </form>
                </div>
                
                <div class="form-card mt-4">
                    <h5 class="mb-4">Change Password</h5>
                    <form action="<?= SITE_URL ?>/controllers/AuthController.php?action=change-password" method="POST">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Current Password *</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">New Password *</label>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Confirm New Password *</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-key me-2"></i>Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
