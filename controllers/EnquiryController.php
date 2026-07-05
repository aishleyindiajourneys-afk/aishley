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
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL);
    }
    
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $tourId = $_POST['tour_id'] ?? null;
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message']);
    
    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($message)) {
        setFlash('error', 'Please fill all required fields');
        redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
    }
    
    // Insert enquiry
    $sql = "INSERT INTO enquiries (name, email, phone, tour_id, subject, message, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([$name, $email, $phone, $tourId, $subject, $message, $_SERVER['REMOTE_ADDR']]);
        
        // Send email notification to admin
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
        
        sendEmail($adminEmail, $emailSubject, $emailBody);
        
        setFlash('success', 'Thank you for your enquiry! We will contact you soon.');
    } catch (PDOException $e) {
        setFlash('error', 'Failed to submit enquiry. Please try again.');
    }
    
    redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
}
