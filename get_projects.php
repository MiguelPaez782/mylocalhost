<?php
require_once 'config.php';

try {
    global $pdo;

    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    if ($search !== '') {
        $stmt = $pdo->prepare("SELECT project_name, project_path, project_icon 
                               FROM projects_address 
                               WHERE project_name LIKE :search
                               ORDER BY created_at DESC");
        $stmt->execute([':search' => '%' . $search . '%']);
    } else {
        $stmt = $pdo->query("SELECT project_name, project_path, project_icon 
                             FROM projects_address 
                             ORDER BY created_at DESC");
    }

    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($projects) {
        foreach ($projects as $project) {
            $name  = htmlspecialchars($project['project_name']);
            $path  = htmlspecialchars($project['project_path']);
            $icon  = htmlspecialchars($project['project_icon']);
            $alt   = "icon-" . strtolower(str_replace(" ", "-", $name));

            echo '
                <a href="' . $path . '" class="a-btn-project" target="_blank">
                    <div class="icon-project">
                        <img src="' . $icon . '" alt="' . $alt . '">
                    </div>
                    <span class="title-project">' . $name . '</span>
                </a>
            ';
        }
    } else {
        echo "<p>No projects found.</p>";
    }

} catch (PDOException $e) {
    echo "Error loading projects: " . $e->getMessage();
}
?>