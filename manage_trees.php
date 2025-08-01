<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$treesFile = 'trees.json';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }

    $action = $_POST['action'] ?? '';
    
    // Load existing trees
    $treesData = ['trees' => []];
    if (file_exists($treesFile)) {
        $treesData = json_decode(file_get_contents($treesFile), true);
        if (!$treesData || !isset($treesData['trees'])) {
            $treesData = ['trees' => []];
        }
    }

    switch ($action) {
        case 'add':
            $newTree = createNewTree($_POST);
            $treesData['trees'][] = $newTree;
            break;
            
        case 'edit':
            $treeId = $_POST['treeId'] ?? '';
            if (!$treeId) {
                throw new Exception('Tree ID required for editing');
            }
            
            $treeIndex = findTreeIndex($treesData['trees'], $treeId);
            if ($treeIndex === -1) {
                throw new Exception('Tree not found');
            }
            
            $treesData['trees'][$treeIndex] = updateTree($treesData['trees'][$treeIndex], $_POST);
            break;
            
        case 'delete':
            $treeId = $_POST['treeId'] ?? '';
            if (!$treeId) {
                throw new Exception('Tree ID required for deletion');
            }
            
            $treeIndex = findTreeIndex($treesData['trees'], $treeId);
            if ($treeIndex === -1) {
                throw new Exception('Tree not found');
            }
            
            // Delete associated files
            $tree = $treesData['trees'][$treeIndex];
            if (isset($tree['dataFile']) && file_exists($tree['dataFile'])) {
                unlink($tree['dataFile']);
            }
            if (isset($tree['siteDataFile']) && file_exists($tree['siteDataFile'])) {
                unlink($tree['siteDataFile']);
            }
            if (isset($tree['image']) && file_exists($tree['image'])) {
                unlink($tree['image']);
            }
            
            array_splice($treesData['trees'], $treeIndex, 1);
            break;
            
        default:
            throw new Exception('Invalid action');
    }

    // Save trees data
    if (file_put_contents($treesFile, json_encode($treesData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
        throw new Exception('Failed to save trees data');
    }

    echo json_encode(['success' => true, 'message' => ucfirst($action) . ' completed successfully']);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function createNewTree($data) {
    $treeId = 'tree_' . time() . '_' . uniqid();
    $title = $data['title'] ?? 'Untitled Tree';
    $subtitleRaw = $data['subtitle'] ?? '';
    $language = $data['language'] ?? 'en';
    
    // Parse subtitle as JSON or create single language entry
    $subtitle = [];
    if ($subtitleRaw) {
        $parsedSubtitle = json_decode($subtitleRaw, true);
        if ($parsedSubtitle && is_array($parsedSubtitle)) {
            $subtitle = $parsedSubtitle;
        } else {
            $subtitle[$language] = $subtitleRaw;
        }
    }
    
    $tree = [
        'id' => $treeId,
        'title' => [
            $language => $title
        ],
        'subtitle' => $subtitle,
        'image' => null,
        'dataFile' => "data_{$treeId}.json",
        'siteDataFile' => "site_data_{$treeId}.json",
        'createdAt' => date('c'),
        'updatedAt' => date('c')
    ];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tree['image'] = handleImageUpload($_FILES['image'], $treeId);
    }

    // Create initial data files
    createInitialDataFiles($tree);

    return $tree;
}

function updateTree($existingTree, $data) {
    $language = $data['language'] ?? 'en';
    $title = $data['title'] ?? $existingTree['title'][$language] ?? '';
    $subtitleRaw = $data['subtitle'] ?? '';
    
    // Update title for the current language
    $existingTree['title'][$language] = $title;
    
    // Parse subtitle as JSON or update single language entry
    if ($subtitleRaw) {
        $parsedSubtitle = json_decode($subtitleRaw, true);
        if ($parsedSubtitle && is_array($parsedSubtitle)) {
            // Merge with existing subtitle data
            $existingTree['subtitle'] = array_merge($existingTree['subtitle'] ?? [], $parsedSubtitle);
        } else {
            // Update only current language
            if (!isset($existingTree['subtitle'])) {
                $existingTree['subtitle'] = [];
            }
            $existingTree['subtitle'][$language] = $subtitleRaw;
        }
    }
    
    $existingTree['updatedAt'] = date('c');

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Delete old image if exists
        if ($existingTree['image'] && file_exists($existingTree['image'])) {
            unlink($existingTree['image']);
        }
        $existingTree['image'] = handleImageUpload($_FILES['image'], $existingTree['id']);
    }

    return $existingTree;
}

function handleImageUpload($file, $treeId) {
    $uploadDir = 'tree_images/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $imageInfo = getimagesize($file['tmp_name']);
    if (!$imageInfo) {
        throw new Exception('Invalid image file');
    }

    $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP];
    if (!in_array($imageInfo[2], $allowedTypes)) {
        throw new Exception('Only JPEG, PNG, and WebP images are allowed');
    }

    $extension = image_type_to_extension($imageInfo[2], false);
    $fileName = $treeId . '_cover.' . $extension;
    $filePath = $uploadDir . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception('Failed to upload image');
    }

    return $filePath;
}

function createInitialDataFiles($tree) {
    // Create empty family tree data file
    $familyTreeData = [
        'familyTree' => null
    ];
    file_put_contents($tree['dataFile'], json_encode($familyTreeData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // Create initial site data file
    $siteData = [
        'family-name' => '',
        'subtitle' => $tree['subtitle']
    ];
    file_put_contents($tree['siteDataFile'], json_encode($siteData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function findTreeIndex($trees, $treeId) {
    foreach ($trees as $index => $tree) {
        if ($tree['id'] === $treeId) {
            return $index;
        }
    }
    return -1;
}
?>