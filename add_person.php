<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Get data file based on tree parameter
function getDataFile($input = null) {
    $treeId = null;
    
    // Try to get tree ID from various sources
    if ($input && isset($input['tree'])) {
        $treeId = $input['tree'];
    } else {
        $treeId = $_GET['tree'] ?? $_POST['tree'] ?? $_GET['id'] ?? $_POST['id'] ?? null;
        if (!$treeId) {
            // Try to get from input data
            $inputData = json_decode(file_get_contents('php://input'), true);
            $treeId = $inputData['tree'] ?? $inputData['id'] ?? null;
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

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    
    // Get the correct data file based on the tree ID from input
    $dataJsonFile = getDataFile($input);

    if (!isset($input['name'])) {
        throw new Exception('Missing required fields');
    }

    $newPerson = [
        'id' => 'person_' . time() . '_' . uniqid(),
        'name' => $input['name'],
        'details' => $input['details'] ?? ['pt' => '', 'en' => '', 'de' => ''],
        'status' => $input['status'] ?? 'alive',
        'children' => []
    ];

    if ($newPerson['status'] === 'deceased' && isset($input['deceasedDate'])) {
        $newPerson['deceasedDate'] = $input['deceasedDate'];
    } else if (isset($input['birthDate'])) {
        $newPerson['birthDate'] = $input['birthDate'];
    }

    if (!file_exists($dataJsonFile)) {
        touch($dataJsonFile);
    }

    $jsonData = file_get_contents($dataJsonFile);
    $data = json_decode($jsonData, true);

    if (empty($data) || !isset($data['familyTree']) || $data['familyTree'] === null) {
        $data = ['familyTree' => $newPerson];
        if (file_put_contents($dataJsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            echo json_encode(['success' => true, 'message' => 'First family member added successfully']);
        } else {
            throw new Exception('Failed to save data');
        }
        exit;
    }

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

    // Helper function to find parent and add sibling
    function findParentAndAddSibling(&$tree, $siblingId, $newPerson) {
        return findParentAndAddChild($tree, $siblingId, $newPerson);
    }

    // Helper function to find the parent of a person and add a new child
    function findParentAndAddChild(&$person, $childId, $newPerson) {
        // Check if any of this person's children is the target
        if (isset($person['children'])) {
            foreach ($person['children'] as $child) {
                if ($child['id'] === $childId) {
                    // Found the target child, add new person as sibling
                    $person['children'][] = $newPerson;
                    sortChildrenByBirthDate($person['children']);
                    return true;
                }
            }
            // Recursively search in grandchildren
            foreach ($person['children'] as &$child) {
                if (findParentAndAddChild($child, $childId, $newPerson)) {
                    return true;
                }
            }
        }
        // Check spouse's children
        if (isset($person['spouse']) && isset($person['spouse']['children'])) {
            foreach ($person['spouse']['children'] as $child) {
                if ($child['id'] === $childId) {
                    // Found the target child in spouse's children, add new person as sibling
                    $person['spouse']['children'][] = $newPerson;
                    sortChildrenByBirthDate($person['spouse']['children']);
                    return true;
                }
            }
            // Recursively search in spouse's grandchildren
            foreach ($person['spouse']['children'] as &$child) {
                if (findParentAndAddChild($child, $childId, $newPerson)) {
                    return true;
                }
            }
        }
        return false;
    }

    // Helper function to add a parent (this is complex and might need special handling)
    function addParent(&$tree, $childId, $newParent) {
        // For now, we'll add the parent as a child relationship instead
        // This is a simplified implementation - proper parent addition would require tree restructuring
        return findAndUpdate($tree, $childId, $newParent, 'child');
    }

    // Recursive function to find and update the person
    function findAndUpdate(&$person, $parentId, $newPerson, $relationship) {
        if (isset($person['id']) && $person['id'] === $parentId) {
            switch ($relationship) {
                case 'child':
                    if (!isset($person['children'])) {
                        $person['children'] = [];
                    }
                    $person['children'][] = $newPerson;
                    // Sort children by birth date after adding
                    sortChildrenByBirthDate($person['children']);
                    break;
                case 'spouse':
                    $person['spouse'] = $newPerson;
                    break;
                case 'sibling':
                    // For siblings, we need to add to the parent's children array
                    // Find the parent of the current person and add the new person as their child
                    return findParentAndAddSibling($person, $parentId, $newPerson);
                case 'parent':
                    // For parent relationship, the new person becomes the parent of the selected person
                    // This is more complex as it requires restructuring the tree
                    return addParent($person, $parentId, $newPerson);
                // Other relationships can be added here
            }
            return true;
        }

        if (isset($person['children'])) {
            foreach ($person['children'] as &$child) {
                if (findAndUpdate($child, $parentId, $newPerson, $relationship)) {
                    return true;
                }
            }
        }
        
        if (isset($person['spouse'])) {
            if (findAndUpdate($person['spouse'], $parentId, $newPerson, $relationship)) {
                return true;
            }
        }

        return false;
    }

    $relationship = $input['relationship'];
    $parentId = $input['parentId'];

    if (findAndUpdate($data['familyTree'], $parentId, $newPerson, $relationship)) {
        if (file_put_contents($dataJsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            echo json_encode(['success' => true, 'message' => 'Family member added successfully']);
        } else {
            throw new Exception('Failed to save data to file: ' . $dataJsonFile);
        }
    } else {
        throw new Exception('Parent not found with ID: ' . $parentId . ' in relationship: ' . $relationship);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
