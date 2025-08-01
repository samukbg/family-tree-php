<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configuration
$photosJsonFile = './images/photos.json';

try {
    // Validate request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['personName'])) {
        throw new Exception('Missing personName field');
    }

    $personName = trim($input['personName']);

    // Load existing photos mapping
    if (!file_exists($photosJsonFile)) {
        throw new Exception('Photos mapping file not found');
    }

    $jsonData = file_get_contents($photosJsonFile);
    $photosMapping = json_decode($jsonData, true);

    if ($photosMapping === null) {
        throw new Exception('Invalid JSON in photos mapping file');
    }

    // Check if person has a photo
    if (!isset($photosMapping[$personName])) {
        throw new Exception('No photo found for this person');
    }

    $filename = $photosMapping[$personName];
    $imagePath = './images/' . $filename;

    // Delete the image file if it exists
    if (file_exists($imagePath)) {
        if (!unlink($imagePath)) {
            throw new Exception('Failed to delete image file');
        }
    }

    // Remove from mapping
    unset($photosMapping[$personName]);

    // Save updated mapping
    if (!file_put_contents($photosJsonFile, json_encode($photosMapping, JSON_PRETTY_PRINT))) {
        throw new Exception('Failed to update photos mapping');
    }

    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Photo deleted successfully',
        'personName' => $personName,
        'filename' => $filename
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