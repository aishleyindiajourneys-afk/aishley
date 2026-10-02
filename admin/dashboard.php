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
$recentEnquiries = $admin->getRecentEnquiries(8);
$enquiryTrend = $admin->getEnquiryTrend(14);
$leadSources = $admin->getLeadSources();
$topTours = $admin->getTopEnquiryTours(5);
$health = $admin->getContentHealth();
$flash = getFlash();
$newPct = $stats['total_enquiries'] > 0 ? round(($stats['new_enquiries'] / $stats['total_enquiries']) * 100) : 0;
$contactedPct = $stats['total_enquiries'] > 0 ? round(($stats['contacted_enquiries'] / $stats['total_enquiries']) * 100) : 0;
$closedPct = $stats['total_enquiries'] > 0 ? round(($stats['closed_enquiries'] / $stats['total_enquiries']) * 100) : 0;
$chartLabels = json_encode(array_column($enquiryTrend, 'label'));
$chartData = json_encode(array_column($enquiryTrend, 'total'));
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
        .insight-card { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.06); }
        .kpi-label { font-size: 13px; color: #6c757d; margin-bottom: 0; }
        .kpi-sub { font-size: 12px; color: #888; }
        .pipeline-bar { height: 10px; border-radius: 8px; overflow: hidden; background: #eee; display: flex; }
        .pipeline-bar span { display: block; height: 100%; }
        .alert-item { border-left: 4px solid #FF631E; background: #fff7f3; padding: 12px 14px; border-radius: 8px; margin-bottom: 10px; }
        .alert-item.ok { border-left-color: #198754; background: #f3fff8; }
        .source-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f1f1f1; }
        .quick-link { display: block; padding: 10px 12px; border-radius: 8px; background: #f8f9fa; color: #201966; text-decoration: none; margin-bottom: 8px; }
        .quick-link:hover { background: #201966; color: #fff; }
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
                    <a class="nav-link" href="slider.php">
                        <i class="fas fa-sliders-h"></i> Hero Slider
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
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h2 class="mb-0">Dashboard</h2>
                            <p class="text-muted mb-0">Welcome back, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?> · <?= date('D, d M Y') ?></p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="enquiries.php" class="btn btn-primary"><i class="fas fa-headset me-2"></i><?= $stats['new_enquiries'] ?> new leads</a>
                            <a href="<?= SITE_URL ?>" target="_blank" class="btn btn-outline-primary"><i class="fas fa-external-link-alt me-2"></i>View Website</a>
                        </div>
                    </div>
                </div>
                
                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3"><i class="fas fa-bolt"></i></div>
                                    <div>
                                        <h3 class="mb-0"><?= $stats['today_enquiries'] ?></h3>
                                        <p class="kpi-label">Leads today</p>
                                        <span class="kpi-sub"><?= $stats['week_enquiries'] ?> in last 7 days</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3"><i class="fas fa-inbox"></i></div>
                                    <div>
                                        <h3 class="mb-0"><?= $stats['new_enquiries'] ?></h3>
                                        <p class="kpi-label">Need follow-up</p>
                                        <span class="kpi-sub"><?= $stats['total_enquiries'] ?> total enquiries</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3"><i class="fas fa-window-restore"></i></div>
                                    <div>
                                        <h3 class="mb-0"><?= $stats['homepage_leads'] ?></h3>
                                        <p class="kpi-label">Homepage popup leads</p>
                                        <span class="kpi-sub"><?= $stats['contacted_enquiries'] ?> already contacted</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3"><i class="fas fa-users"></i></div>
                                    <div>
                                        <h3 class="mb-0"><?= $stats['total_users'] ?></h3>
                                        <p class="kpi-label">Registered users</p>
                                        <span class="kpi-sub"><?= $stats['total_favorites'] ?> saved tours</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <div class="card insight-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Enquiry trend (14 days)</h5>
                                    <span class="text-muted small"><?= array_sum(array_column($enquiryTrend, 'total')) ?> leads in this period</span>
                                </div>
                                <canvas id="enquiryChart" height="110"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card insight-card h-100">
                            <div class="card-body">
                                <h5 class="mb-3">Lead pipeline</h5>
                                <div class="pipeline-bar mb-3">
                                    <span style="width: <?= $newPct ?>%; background: #dc3545;"></span>
                                    <span style="width: <?= $contactedPct ?>%; background: #ffc107;"></span>
                                    <span style="width: <?= $closedPct ?>%; background: #198754;"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2"><span><span class="badge bg-danger me-1">&nbsp;</span> New</span><strong><?= $stats['new_enquiries'] ?></strong></div>
                                <div class="d-flex justify-content-between mb-2"><span><span class="badge bg-warning text-dark me-1">&nbsp;</span> Contacted</span><strong><?= $stats['contacted_enquiries'] ?></strong></div>
                                <div class="d-flex justify-content-between mb-3"><span><span class="badge bg-success me-1">&nbsp;</span> Closed</span><strong><?= $stats['closed_enquiries'] ?></strong></div>
                                <hr>
                                <h6 class="mb-2">Lead source</h6>
                                <?php if ($leadSources): ?>
                                    <?php foreach ($leadSources as $source): ?>
                                        <div class="source-row">
                                            <span><?= htmlspecialchars($source['source']) ?></span>
                                            <strong><?= (int)$source['total'] ?></strong>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted small mb-0">No leads yet</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row g-4 mb-4">
                    <div class="col-lg-4">
                        <div class="card insight-card h-100">
                            <div class="card-body">
                                <h5 class="mb-3">Needs attention</h5>
                                <?php if ($stats['new_enquiries'] > 0): ?>
                                    <div class="alert-item"><a href="enquiries.php" class="text-decoration-none text-dark"><strong><?= $stats['new_enquiries'] ?> new enquiries</strong> waiting for a reply.</a></div>
                                <?php endif; ?>
                                <?php if ($health['tours_without_image'] > 0): ?>
                                    <div class="alert-item"><a href="tours.php" class="text-decoration-none text-dark"><strong><?= $health['tours_without_image'] ?> tours</strong> are missing a featured image.</a></div>
                                <?php endif; ?>
                                <?php if ($health['destinations_without_image'] > 0): ?>
                                    <div class="alert-item"><a href="destinations.php" class="text-decoration-none text-dark"><strong><?= $health['destinations_without_image'] ?> destinations</strong> have no image or banner.</a></div>
                                <?php endif; ?>
                                <?php if ($health['inactive_tours'] > 0): ?>
                                    <div class="alert-item"><strong><?= $health['inactive_tours'] ?> tours</strong> are inactive and hidden from the site.</div>
                                <?php endif; ?>
                                <?php if ($health['unpublished_blogs'] > 0): ?>
                                    <div class="alert-item"><a href="blogs.php" class="text-decoration-none text-dark"><strong><?= $health['unpublished_blogs'] ?> blog posts</strong> are still drafts.</a></div>
                                <?php endif; ?>
                                <?php if ($stats['new_enquiries'] === 0 && $health['tours_without_image'] === 0 && $health['destinations_without_image'] === 0 && $health['inactive_tours'] === 0 && $health['unpublished_blogs'] === 0): ?>
                                    <div class="alert-item ok mb-0">All clear. No urgent content or lead issues.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card insight-card h-100">
                            <div class="card-body">
                                <h5 class="mb-3">Catalogue snapshot</h5>
                                <div class="d-flex justify-content-between mb-2"><span>Active tours</span><strong><?= $stats['active_tours'] ?> / <?= $stats['total_tours'] ?></strong></div>
                                <div class="d-flex justify-content-between mb-2"><span>Featured tours</span><strong><?= $health['featured_tours'] ?></strong></div>
                                <div class="d-flex justify-content-between mb-2"><span>Destinations</span><strong><?= $stats['total_destinations'] ?></strong></div>
                                <div class="d-flex justify-content-between mb-3"><span>Published blogs</span><strong><?= $stats['total_blogs'] - $health['unpublished_blogs'] ?></strong></div>
                                <h6 class="mb-2">Most enquired tours</h6>
                                <?php if ($topTours): ?>
                                    <?php foreach ($topTours as $tour): ?>
                                        <div class="source-row"><span><?= htmlspecialchars($tour['title']) ?></span><strong><?= (int)$tour['total'] ?></strong></div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted small mb-0">Tour-page enquiries will appear here.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card insight-card h-100">
                            <div class="card-body">
                                <h5 class="mb-3">Quick actions</h5>
                                <a class="quick-link" href="enquiries.php"><i class="fas fa-envelope me-2"></i>Follow up enquiries</a>
                                <a class="quick-link" href="tours-create.php"><i class="fas fa-plus me-2"></i>Add a new tour</a>
                                <a class="quick-link" href="slider.php"><i class="fas fa-sliders-h me-2"></i>Update homepage slider</a>
                                <a class="quick-link" href="destinations.php"><i class="fas fa-image me-2"></i>Fix destination images</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Latest enquiries</h5>
                        <a href="enquiries.php" class="btn btn-sm btn-outline-primary">View all</a>
                    </div>
                    <div class="card-body">
                        <?php if ($recentEnquiries): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Contact</th>
                                            <th>Source / tour</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentEnquiries as $enquiry): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($enquiry['name']) ?></td>
                                                <td>
                                                    <div><?= htmlspecialchars($enquiry['email']) ?></div>
                                                    <small class="text-muted"><?= htmlspecialchars($enquiry['phone']) ?></small>
                                                </td>
                                                <td>
                                                    <?php if ($enquiry['subject'] === 'Homepage Lead'): ?>
                                                        <span class="badge bg-primary">Homepage popup</span>
                                                    <?php elseif (!empty($enquiry['tour_title'])): ?>
                                                        <?= htmlspecialchars($enquiry['tour_title']) ?>
                                                    <?php else: ?>
                                                        <?= htmlspecialchars($enquiry['subject'] ?: 'General') ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $enquiry['status'] == 'new' ? 'danger' : ($enquiry['status'] == 'contacted' ? 'warning text-dark' : 'success') ?>">
                                                        <?= ucfirst($enquiry['status']) ?>
                                                    </span>
                                                </td>
                                                <td><?= formatDate($enquiry['created_at'], 'd M Y H:i') ?></td>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('enquiryChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= $chartLabels ?>,
                    datasets: [{
                        label: 'Enquiries',
                        data: <?= $chartData ?>,
                        backgroundColor: '#FF631E',
                        borderRadius: 6,
                        maxBarThickness: 28
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    </script>
</body>
</html>
