<?php
require_once 'config.php';

function deleteProject($pdo) {
    $defaultIconName = 'default.png';

    $listProjects = function() use ($pdo) {
        try {
            $stmt = $pdo->query("SELECT id, project_name FROM projects_address ORDER BY project_name ASC");
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($projects) {
                foreach ($projects as $project) {
                    echo "<option value='" . htmlspecialchars($project['id'], ENT_QUOTES) . "'>"
                         . htmlspecialchars($project['project_name']) .
                         "</option>";
                }
            } else {
                echo "<option value='0'>There are no projects</option>";
            }
        } catch (PDOException $e) {
            echo "<option value='0'>Error loading projects</option>";
        }
    };

    $performDeletion = function($projectId) use ($pdo, $defaultIconName) {
        $projectId = intval($projectId);
        if ($projectId <= 0) return false;

        try {
            $stmt = $pdo->prepare("SELECT project_name, project_path, project_icon FROM projects_address WHERE id = :id");
            $stmt->execute([':id' => $projectId]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$project) return false;

            $projectNameFolder = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($project['project_name']));
            $projectDir = __DIR__ . "/projects/" . $projectNameFolder;

            if (is_dir($projectDir)) {
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($projectDir, FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );
                foreach ($files as $file) {
                    if ($file->isDir()) {
                        rmdir($file->getRealPath());
                    } else {
                        unlink($file->getRealPath());
                    }
                }
                rmdir($projectDir);
            }

            $iconBase = basename($project['project_icon']);
            if ($iconBase !== $defaultIconName) {
                $iconPath = __DIR__ . "/assets/img/projects-icons/" . $iconBase;
                if (file_exists($iconPath)) unlink($iconPath);
            }

            $stmt2 = $pdo->prepare("DELETE FROM projects_address WHERE id = :id");
            $stmt2->execute([':id' => $projectId]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    };

    if (!isset($_GET['deleteProject'])) {
        $listProjects();
        return;
    }

    if (!isset($_GET['selectProjectDelete'])) {
        header("Location: index.php");
        exit;
    }

    $projectId = intval($_GET['selectProjectDelete']);
    if ($projectId <= 0) {
        header("Location: index.php");
        exit;
    }

    if (!isset($_GET['confirm'])) {
        try {
            $stmt = $pdo->prepare("SELECT id, project_name, project_icon FROM projects_address WHERE id = :id");
            $stmt->execute([':id' => $projectId]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$project) {
                header("Location: index.php");
                exit;
            }
        } catch (PDOException $e) {
            die("Error al cargar proyecto: " . $e->getMessage());
        }

        $safeName = htmlspecialchars($project['project_name'], ENT_QUOTES);
        $iconUrl = htmlspecialchars($project['project_icon'], ENT_QUOTES);
        $yesUrl  = "delete_project.php?deleteProject=1&selectProjectDelete={$projectId}&confirm=yes";
        $noUrl   = "index.php";

        echo "
        <!doctype html>
        <html lang='es'>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width,initial-scale=1'>
            <title>Confirm deletion</title>
            <link rel='stylesheet' href='assets/css/delete_style.css'>

        </head>
        <body>
            <div class='card'>
                <h2>Delete project</h2>
                <p>¿Are you sure you want to delete the project? <strong>{$safeName}</strong>?</p>
                <div style='margin:16px 0;'><img class='icon' src='{$iconUrl}' alt='icon-{$safeName}'></div>
                <div class='actions'>
                    <a class='btn-delete' href='" . htmlspecialchars($yesUrl, ENT_QUOTES) . "'>Yes, delete</a>
                    <a class='btn-cancel' href='" . htmlspecialchars($noUrl, ENT_QUOTES) . "'>Cancel</a>
                </div>
            </div>
        </body>
        </html>";
        exit;
    }

    if ($_GET['confirm'] === 'yes') {
        $ok = $performDeletion($projectId);
        if ($ok) {
            header("Location: index.php?deleted=1");
            exit;
        } else {
            header("Location: index.php?deleted=0");
            exit;
        }
    } else {
        header("Location: index.php");
        exit;
    }
}

deleteProject($pdo);

?>