<?php
header('Content-Type: application/json');

// Get site data file based on tree parameter
function getSiteDataFile() {
    $treeId = $_GET['tree'] ?? $_POST['tree'] ?? null;
    if (!$treeId) {
        // Try to get from input data
        $input = json_decode(file_get_contents('php://input'), true);
        $treeId = $input['tree'] ?? null;
    }
    
    if ($treeId) {
        $treesFile = './trees.json';
        if (file_exists($treesFile)) {
            $treesData = json_decode(file_get_contents($treesFile), true);
            $trees = $treesData['trees'] ?? [];
            foreach ($trees as $tree) {
                if ($tree['id'] === $treeId) {
                    return './' . $tree['siteDataFile'];
                }
            }
        }
    }
    return './site_data.json'; // fallback
}

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $siteDataFile = getSiteDataFile();
    file_put_contents($siteDataFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data.']);
}
?>
