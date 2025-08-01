<?php
header('Content-Type: application/json');

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

function findAndRemove(&$node, $id) {
    // Check current node
    if (isset($node['children'])) {
        foreach ($node['children'] as $key => $child) {
            if ($child['id'] === $id) {
                array_splice($node['children'], $key, 1);
                return true;
            }
            if (findAndRemove($node['children'][$key], $id)) {
                return true;
            }
        }
    }
    // Check spouse
    if (isset($node['spouse'])) {
        if ($node['spouse']['id'] === $id) {
            unset($node['spouse']);
            return true;
        }
        if (isset($node['spouse']['children'])) {
            foreach ($node['spouse']['children'] as $key => $child) {
                if ($child['id'] === $id) {
                    array_splice($node['spouse']['children'], $key, 1);
                    return true;
                }
                if (findAndRemove($node['spouse']['children'][$key], $id)) {
                    return true;
                }
            }
        }
    }
    return false;
}

$data = json_decode(file_get_contents('php://input'), true);

if ($data && isset($data['id'])) {
    $jsonFile = getDataFile($data);
    $jsonData = json_decode(file_get_contents($jsonFile), true);

    if (findAndRemove($jsonData['familyTree'], $data['id'])) {
        file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Person not found.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data.']);
}
?>
