<?php
require_once 'config/database.php';
require_once 'config/functions.php';

$settings = getSettings();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #667eea; --secondary: #764ba2; }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .error-content { text-align: center; }
        .error-code { font-size: 150px; font-weight: 700; line-height: 1; }
        .error-message { font-size: 24px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="error-content">
        <div class="error-code">404</div>
        <h1 class="error-message">Page Not Found</h1>
        <p class="mb-4">The page you are looking for doesn't exist or has been moved.</p>
        <a href="<?= SITE_URL ?>" class="btn btn-light btn-lg">
            <i class="fas fa-home me-2"></i>Go Back Home
        </a>
    </div>
</body>
</html>
