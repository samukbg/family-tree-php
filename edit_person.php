<?php
header('Content-Type: application/json');

// Function to sort children by birth date (oldest first)
function sortChildrenByBirthDate(&$children) {
    usort($children, function($a, $b) {
        $birthA = null;
        $birthB = null;
        
        // Extract birth year from birthDate or details
        if (isset($a['birthDate']) && !empty($a['birthDate'])) {
            preg_match('/\d{4}/', $a['birthDate'], $matches);
            if (!empty($matches)) {
                $birthA = (int)$matches[0];
            }
        }
        
        if (isset($b['birthDate']) && !empty($b['birthDate'])) {
            preg_match('/\d{4}/', $b['birthDate'], $matches);
            if (!empty($matches)) {
                $birthB = (int)$matches[0];
            }
        }
        
        // If no birthDate, try to extract from details
        if ($birthA === null && isset($a['details']) && is_array($a['details'])) {
            foreach ($a['details'] as $detail) {
                if (preg_match('/\b(19|20)\d{2}\b/', $detail, $matches)) {
                    $birthA = (int)$matches[0];
                    break;
                }
            }
        }
        
        if ($birthB === null && isset($b['details']) && is_array($b['details'])) {
            foreach ($b['details'] as $detail) {
                if (preg_match('/\b(19|20)\d{2}\b/', $detail, $matches)) {
                    $birthB = (int)$matches[0];
                    break;
                }
            }
        }
        
        // If both have birth dates, sort by birth year (oldest first)
        if ($birthA !== null && $birthB !== null) {
            return $birthA - $birthB;
        }
        
        // If only one has a birth date, put it first
        if ($birthA !== null) return -1;
        if ($birthB !== null) return 1;
        
        // If neither has a birth date, maintain current order
        return 0;
    });
}

// Function to find the parent of a person and re-sort siblings
function findParentAndResort(&$node, $id, $foundPersonId = null) {
    if (isset($node['children'])) {
        foreach ($node['children'] as $child) {
            if (isset($child['id']) && $child['id'] === $id) {
                // Found the person, sort this level's children
                sortChildrenByBirthDate($node['children']);
                return true;
            }
        }
        
        // Recursively search in children
        foreach ($node['children'] as &$child) {
            if (findParentAndResort($child, $id)) {
                return true;
            }
        }
    }
    
    // Check spouse as well
    if (isset($node['spouse']) && isset($node['spouse']['id']) && $node['spouse']['id'] === $id) {
        // This person is a spouse, no siblings to sort
        return true;
    }
    
    if (isset($node['spouse'])) {
        if (findParentAndResort($node['spouse'], $id)) {
            return true;
        }
    }
    
    return false;
}

function findAndUpdate(&$node, $id, $newData) {
    if (isset($node['id']) && $node['id'] === $id) {
        $node['name'] = $newData['name'];
        $node['details'] = $newData['details'];
        $node['status'] = $newData['status'];
        if ($newData['status'] === 'deceased') {
            $node['deceasedDate'] = $newData['deceasedDate'];
            $node['birthDate'] = $newData['birthDate'];
        } else {
            $node['birthDate'] = $newData['birthDate'];
            unset($node['deceasedDate']);
        }
        return true;
    }

    if (isset($node['spouse'])) {
        if (findAndUpdate($node['spouse'], $id, $newData)) {
            return true;
        }
    }

    if (isset($node['children'])) {
        foreach ($node['children'] as &$child) {
            if (findAndUpdate($child, $id, $newData)) {
                return true;
            }
        }
    }

    return false;
}

// Get data file based on tree parameter
function getDataFile($input = null) {
    $treeId = null;
    
    // Try to get tree ID from various sources
    if ($input && isset($input['tree'])) {
        $treeId = $input['tree'];
    } else {
        $treeId = $_GET['tree'] ?? $_POST['tree'] ?? null;
        if (!$treeId) {
            // Try to get from input data
            $inputData = json_decode(file_get_contents('php://input'), true);
            $treeId = $inputData['tree'] ?? null;
        }
    }
    
    if ($treeId) {
        $treesFile = './trees.json';
        if (file_exists($treesFile)) {
            $treesData = json_decode(file_get_contents($treesFile), true);
            $trees = $treesData['trees'] ?? [];
            foreach ($trees as $tree) {
                if ($tree['id'] === $treeId) {
                    return './' . $tree['dataFile'];
                }
            }
        }
    }
    return './data.json'; // fallback
}

$data = json_decode(file_get_contents('php://input'), true);

if ($data && isset($data['id'])) {
    $jsonFile = getDataFile($data);
    $jsonData = json_decode(file_get_contents($jsonFile), true);

    if (findAndUpdate($jsonData['familyTree'], $data['id'], $data)) {
        // Re-sort siblings if birth date was updated
        findParentAndResort($jsonData['familyTree'], $data['id']);
        
        file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Person not found.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data.']);
}
?>
