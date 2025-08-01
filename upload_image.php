<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configuration
$uploadDir = './images/';
$photosJsonFile = './images/photos.json';
$maxFileSize = 5 * 1024 * 1024; // 5MB
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

try {
    // Check if images directory exists, create if not
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create images directory');
        }
    }

    // Validate request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }

    if (!isset($_FILES['image']) || !isset($_POST['personName']) || !isset($_POST['filename'])) {
        throw new Exception('Missing required fields');
    }

    $personName = trim($_POST['personName']);
    $filename = trim($_POST['filename']);
    $uploadedFile = $_FILES['image'];

    // Validate file
    if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error: ' . $uploadedFile['error']);
    }

    if ($uploadedFile['size'] > $maxFileSize) {
        throw new Exception('File too large. Maximum size is 5MB');
    }

    $fileType = mime_content_type($uploadedFile['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPEG, PNG, and GIF allowed');
    }

    // Sanitize and create WebP filename
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($filename, PATHINFO_FILENAME)) . '.webp';
    $targetPath = $uploadDir . $filename;

    // Create image from uploaded file
    $sourceImage = imagecreatefromstring(file_get_contents($uploadedFile['tmp_name']));
    if ($sourceImage === false) {
        throw new Exception('Failed to create image from file');
    }

    // Save image as WebP
    if (!imagewebp($sourceImage, $targetPath, 100)) {
        throw new Exception('Failed to save image as WebP');
    }

    // Free up memory
    imagedestroy($sourceImage);

    // Load existing photos mapping
    $photosMapping = [];
    if (file_exists($photosJsonFile)) {
        $jsonData = file_get_contents($photosJsonFile);
        $photosMapping = json_decode($jsonData, true) ?: [];
    }

    // Update mapping
    $photosMapping[$personName] = $filename;

    // Save updated mapping
    if (!file_put_contents($photosJsonFile, json_encode($photosMapping, JSON_PRETTY_PRINT))) {
        throw new Exception('Failed to update photos mapping');
    }

    // Set proper permissions
    chmod($targetPath, 0644);
    chmod($photosJsonFile, 0644);

    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Photo uploaded successfully',
        'filename' => $filename,
        'personName' => $personName
    ]);

} catch (Exception $e) {
    // Error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
