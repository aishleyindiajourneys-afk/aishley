<?php
/**
 * Enquiry Controller
 * Aishley India Journeys
 */

require_once '../config/database.php';
require_once '../config/functions.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        handleCreateEnquiry();
        break;
    default:
        redirect(SITE_URL);
}

/**
 * Handle Create Enquiry
 */
function handleCreateEnquiry() {
    global $pdo;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL);
    }
    
    ob_start();
    
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $tourId = $_POST['tour_id'] ?? null;
    $subject = sanitize($_POST['subject'] ?? '');
    $destination = sanitize($_POST['destination'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    if ($destination !== '') {
        $message = trim("Preferred destination: $destination\n" . $message);
        if ($subject === '') {
            $subject = 'Enquiry for ' . $destination;
        }
    }
    if ($message === '') {
        $message = 'Lead enquiry from website';
    }
    
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    $respond = function ($success, $text) use ($isAjax) {
        if (ob_get_length()) {
            ob_end_clean();
        }
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => $success, 'message' => $text]);
            exit;
        }
        setFlash($success ? 'success' : 'error', $text);
        redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
    };
    
    if (empty($name) || empty($email) || empty($phone)) {
        $respond(false, 'Please fill all required fields');
    }
    
    $sql = "INSERT INTO enquiries (name, email, phone, tour_id, subject, message, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([$name, $email, $phone, $tourId ?: null, $subject, $message, $_SERVER['REMOTE_ADDR'] ?? '']);
        
        $settings = getSettings();
        $adminEmail = $settings['contact_email'] ?? ADMIN_EMAIL;
        $emailSubject = "New Enquiry from " . $name;
        $emailBody = "
            <h2>New Enquiry Received</h2>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Subject:</strong> $subject</p>
            <p><strong>Message:</strong></p>
            <p>" . nl2br($message) . "</p>
        ";
        try {
            @sendEmail($adminEmail, $emailSubject, $emailBody);
        } catch (Throwable $e) {
            // Enquiry is already saved; skip mail failures
        }
        
        $respond(true, 'Thank you for your enquiry! We will contact you soon.');
    } catch (PDOException $e) {
        $respond(false, 'Failed to submit enquiry. Please try again.');
    }
}
