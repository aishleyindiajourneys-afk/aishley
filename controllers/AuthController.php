<?php
/**
 * Authentication Controller
 * Aishley India Journeys
 */

require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../models/Auth.php';

$auth = new Auth($pdo);
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'admin-login':
        handleAdminLogin($auth);
        break;
    case 'admin-logout':
        handleAdminLogout();
        break;
    case 'user-register':
        handleUserRegister($auth);
        break;
    case 'user-login':
        handleUserLogin($auth);
        break;
    case 'user-logout':
        handleUserLogout();
        break;
    case 'update-profile':
        handleUpdateProfile($auth);
        break;
    case 'change-password':
        handleChangePassword($auth);
        break;
    case 'add-favorite':
        handleAddFavorite($auth);
        break;
    case 'remove-favorite':
        handleRemoveFavorite($auth);
        break;
    default:
        redirect(SITE_URL);
}

/**
 * Handle Admin Login
 */
function handleAdminLogin($auth) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/login.php');
    }
    
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    $admin = $auth->adminLogin($email, $password);
    
    if ($admin) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_role'] = $admin['role'];
        
        setFlash('success', 'Welcome back, ' . $admin['name']);
        redirect(SITE_URL . '/admin/dashboard.php');
    } else {
        setFlash('error', 'Invalid email or password');
        redirect(SITE_URL . '/admin/login.php');
    }
}

/**
 * Handle Admin Logout
 */
function handleAdminLogout() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    session_name(SESSION_NAME);
    session_start();
    setFlash('success', 'Logged out successfully');
    redirect(SITE_URL . '/admin/login.php');
}

/**
 * Handle User Registration
 */
function handleUserRegister($auth) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/register.php');
    }
    
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        setFlash('error', 'Please fill all required fields');
        redirect(SITE_URL . '/register.php');
    }
    
    if ($password !== $confirmPassword) {
        setFlash('error', 'Passwords do not match');
        redirect(SITE_URL . '/register.php');
    }
    
    if (strlen($password) < 6) {
        setFlash('error', 'Password must be at least 6 characters');
        redirect(SITE_URL . '/register.php');
    }
    
    $result = $auth->registerUser($name, $email, $password, $phone, $address);
    
    if ($result['success']) {
        setFlash('success', 'Registration successful! Please login.');
        redirect(SITE_URL . '/login.php');
    } else {
        setFlash('error', $result['message']);
        redirect(SITE_URL . '/register.php');
    }
}

/**
 * Handle User Login
 */
function handleUserLogin($auth) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/login.php');
    }
    
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    $user = $auth->userLogin($email, $password);
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        
        setFlash('success', 'Welcome back, ' . $user['name']);
        redirect(SITE_URL . '/user/dashboard.php');
    } else {
        setFlash('error', 'Invalid email or password');
        redirect(SITE_URL . '/login.php');
    }
}

/**
 * Handle User Logout
 */
function handleUserLogout() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    session_name(SESSION_NAME);
    session_start();
    setFlash('success', 'Logged out successfully');
    redirect(SITE_URL . '/login.php');
}

/**
 * Handle Update Profile
 */
function handleUpdateProfile($auth) {
    if (!isLoggedIn()) {
        redirect(SITE_URL . '/login.php');
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/user/profile.php');
    }
    
    $userId = $_SESSION['user_id'];
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $profileImage = null;
    
    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['profile_image'], UPLOAD_PATH . 'users/');
        if ($uploadResult['success']) {
            $profileImage = 'users/' . $uploadResult['filename'];
        }
    }
    
    $result = $auth->updateUserProfile($userId, $name, $phone, $address, $profileImage);
    
    if ($result['success']) {
        $_SESSION['user_name'] = $name;
        setFlash('success', 'Profile updated successfully');
    } else {
        setFlash('error', $result['message']);
    }
    
    redirect(SITE_URL . '/user/profile.php');
}

/**
 * Handle Change Password
 */
function handleChangePassword($auth) {
    if (!isLoggedIn()) {
        redirect(SITE_URL . '/login.php');
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/user/profile.php');
    }
    
    $userId = $_SESSION['user_id'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if ($newPassword !== $confirmPassword) {
        setFlash('error', 'New passwords do not match');
        redirect(SITE_URL . '/user/profile.php');
    }
    
    if (strlen($newPassword) < 6) {
        setFlash('error', 'Password must be at least 6 characters');
        redirect(SITE_URL . '/user/profile.php');
    }
    
    $result = $auth->changeUserPassword($userId, $currentPassword, $newPassword);
    
    if ($result['success']) {
        setFlash('success', 'Password changed successfully');
    } else {
        setFlash('error', $result['message']);
    }
    
    redirect(SITE_URL . '/user/profile.php');
}

/**
 * Handle Add to Favorites
 */
function handleAddFavorite($auth) {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    $tourId = $_POST['tour_id'] ?? 0;
    
    if (!$tourId) {
        echo json_encode(['success' => false, 'message' => 'Invalid tour']);
        exit;
    }
    
    $result = $auth->addToFavorites($userId, $tourId);
    echo json_encode($result);
    exit;
}

/**
 * Handle Remove from Favorites
 */
function handleRemoveFavorite($auth) {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    $tourId = $_POST['tour_id'] ?? 0;
    
    if (!$tourId) {
        echo json_encode(['success' => false, 'message' => 'Invalid tour']);
        exit;
    }
    
    $result = $auth->removeFromFavorites($userId, $tourId);
    echo json_encode($result);
    exit;
}
