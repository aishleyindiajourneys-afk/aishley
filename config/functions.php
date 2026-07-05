<?php
/**
 * Common Functions
 * Aishley India Journeys
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

/**
 * Sanitize input data
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate slug from string
 */
function generateSlug($string) {
    $slug = strtolower($string);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}

/**
 * Set flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Format price
 */
function formatPrice($price) {
    return '₹' . number_format($price, 2);
}

/**
 * Format date
 */
function formatDate($date, $format = 'd M Y') {
    return date($format, strtotime($date));
}

/**
 * Truncate text
 */
function truncate($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

/**
 * Upload file
 */
function uploadFile($file, $destination, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp']) {
    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    
    if ($fileError !== 0) {
        return ['success' => false, 'message' => 'File upload error'];
    }
    
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($fileExt, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    if ($fileSize > 5 * 1024 * 1024) { // 5MB limit
        return ['success' => false, 'message' => 'File size exceeds 5MB'];
    }
    
    $newFileName = uniqid() . '_' . time() . '.' . $fileExt;
    $uploadPath = $destination . $newFileName;
    
    if (!is_dir($destination)) {
        mkdir($destination, 0777, true);
    }
    
    if (move_uploaded_file($fileTmp, $uploadPath)) {
        return ['success' => true, 'filename' => $newFileName];
    }
    
    return ['success' => false, 'message' => 'Failed to upload file'];
}

/**
 * Delete file
 */
function deleteFile($filePath) {
    if (file_exists($filePath)) {
        unlink($filePath);
        return true;
    }
    return false;
}

/**
 * Send email
 */
function sendEmail($to, $subject, $message, $headers = '') {
    $defaultHeaders = "From: " . ADMIN_EMAIL . "\r\n";
    $defaultHeaders .= "MIME-Version: 1.0\r\n";
    $defaultHeaders .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $defaultHeaders . $headers);
}

/**
 * Pagination
 */
function paginate($total, $perPage, $currentPage) {
    $totalPages = ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset
    ];
}

/**
 * Get site settings
 */
function getSettings() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
    return $stmt->fetch();
}

/**
 * Generate SEO meta tags
 */
function generateMetaTags($title, $description, $keywords = '') {
    $settings = getSettings();
    $siteName = $settings['site_name'] ?? SITE_NAME;
    
    $meta = '<title>' . htmlspecialchars($title) . ' - ' . htmlspecialchars($siteName) . '</title>' . "\n";
    $meta .= '<meta name="description" content="' . htmlspecialchars($description) . '">' . "\n";
    if ($keywords) {
        $meta .= '<meta name="keywords" content="' . htmlspecialchars($keywords) . '">' . "\n";
    }
    $meta .= '<meta name="robots" content="index, follow">' . "\n";
    $meta .= '<meta property="og:title" content="' . htmlspecialchars($title) . '">' . "\n";
    $meta .= '<meta property="og:description" content="' . htmlspecialchars($description) . '">' . "\n";
    $meta .= '<meta property="og:type" content="website">' . "\n";
    $meta .= '<meta property="og:url" content="' . SITE_URL . $_SERVER['REQUEST_URI'] . '">' . "\n";
    
    return $meta;
}

/**
 * Check if slug exists
 */
function slugExists($table, $slug, $excludeId = null) {
    global $pdo;
    $sql = "SELECT id FROM $table WHERE slug = ?";
    $params = [$slug];
    
    if ($excludeId) {
        $sql .= " AND id != ?";
        $params[] = $excludeId;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch() !== false;
}

/**
 * Generate unique slug
 */
function generateUniqueSlug($table, $string, $excludeId = null) {
    $slug = generateSlug($string);
    $counter = 1;
    $originalSlug = $slug;
    
    while (slugExists($table, $slug, $excludeId)) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}
