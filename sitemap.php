<?php
require_once 'config/database.php';
require_once 'config/functions.php';

header('Content-Type: application/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Static pages
$staticPages = [
    SITE_URL => '1.0',
    SITE_URL . '/tours.php' => '0.9',
    SITE_URL . '/destinations.php' => '0.9',
    SITE_URL . '/blogs.php' => '0.8',
    SITE_URL . '/gallery.php' => '0.7',
    SITE_URL . '/contact.php' => '0.6',
    SITE_URL . '/faq.php' => '0.6',
    SITE_URL . '/testimonials.php' => '0.6',
    SITE_URL . '/login.php' => '0.3',
    SITE_URL . '/register.php' => '0.3',
];

foreach ($staticPages as $url => $priority) {
    echo '<url>';
    echo '<loc>' . htmlspecialchars($url) . '</loc>';
    echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
    echo '<changefreq>weekly</changefreq>';
    echo '<priority>' . $priority . '</priority>';
    echo '</url>';
}

// Dynamic tours
$stmt = $pdo->query("SELECT slug, updated_at FROM tours WHERE status = 'active'");
while ($row = $stmt->fetch()) {
    echo '<url>';
    echo '<loc>' . htmlspecialchars(SITE_URL . '/tour-details.php?slug=' . $row['slug']) . '</loc>';
    echo '<lastmod>' . date('Y-m-d', strtotime($row['updated_at'])) . '</lastmod>';
    echo '<changefreq>weekly</changefreq>';
    echo '<priority>0.8</priority>';
    echo '</url>';
}

// Dynamic destinations
$stmt = $pdo->query("SELECT slug, updated_at FROM destinations WHERE status = 'active'");
while ($row = $stmt->fetch()) {
    echo '<url>';
    echo '<loc>' . htmlspecialchars(SITE_URL . '/destinations.php') . '</loc>';
    echo '<lastmod>' . date('Y-m-d', strtotime($row['updated_at'])) . '</lastmod>';
    echo '<changefreq>weekly</changefreq>';
    echo '<priority>0.7</priority>';
    echo '</url>';
}

// Dynamic blogs
$stmt = $pdo->query("SELECT slug, updated_at FROM blogs WHERE status = 'published'");
while ($row = $stmt->fetch()) {
    echo '<url>';
    echo '<loc>' . htmlspecialchars(SITE_URL . '/blog-details.php?slug=' . $row['slug']) . '</loc>';
    echo '<lastmod>' . date('Y-m-d', strtotime($row['updated_at'])) . '</lastmod>';
    echo '<changefreq>weekly</changefreq>';
    echo '<priority>0.6</priority>';
    echo '</url>';
}

// Dynamic pages (CMS)
$stmt = $pdo->query("SELECT slug, updated_at FROM pages WHERE status = 'active'");
while ($row = $stmt->fetch()) {
    echo '<url>';
    echo '<loc>' . htmlspecialchars(SITE_URL . '/page.php?slug=' . $row['slug']) . '</loc>';
    echo '<lastmod>' . date('Y-m-d', strtotime($row['updated_at'])) . '</lastmod>';
    echo '<changefreq>monthly</changefreq>';
    echo '<priority>0.5</priority>';
    echo '</url>';
}

echo '</urlset>';
