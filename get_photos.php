<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Configuration
$photosJsonFile = './images/photos.json';

try {
    // Check if photos mapping file exists
    if (!file_exists($photosJsonFile)) {
        // Create empty mapping file if it doesn't exist
        $emptyMapping = [];
        file_put_contents($photosJsonFile, json_encode($emptyMapping, JSON_PRETTY_PRINT));
        chmod($photosJsonFile, 0644);
    }

    // Read photos mapping
    $jsonData = file_get_contents($photosJsonFile);
    $photosMapping = json_decode($jsonData, true);

    if ($photosMapping === null) {
        throw new Exception('Invalid JSON in photos mapping file');
    }

    // Verify that image files still exist and clean up mapping
    $cleanedMapping = [];
    foreach ($photosMapping as $personName => $filename) {
        $imagePath = './images/' . $filename;
        if (file_exists($imagePath)) {
            $cleanedMapping[$personName] = $filename;
        }
    }

    // Update the mapping file if we removed any entries
    if (count($cleanedMapping) < count($photosMapping)) {
        file_put_contents($photosJsonFile, json_encode($cleanedMapping, JSON_PRETTY_PRINT));
    }

    // Success response
    echo json_encode([
        'success' => true,
        'data' => $cleanedMapping,
        'count' => count($cleanedMapping)
    ]);

} catch (Exception $e) {
    // Error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'data' => []
    ]);
}
?>