<?php

if (isset($_POST['createProject'])) {
    global $pdo;

    $projectName = trim($_POST['projectName']);
    $projectNameFolder = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($projectName));

    $projectsDir = __DIR__ . "/projects/" . $projectNameFolder;
    $iconsDir = __DIR__ . "/assets/img/projects-icons/";
    $defaultIcon = "default.png";

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM projects_address WHERE project_name = :name");
    $stmt->execute([':name' => $projectName]);
    $exists = $stmt->fetchColumn();

    if ($exists > 0) {
        header("Location: index.php?error=ProjectAlreadyExists");
        exit;
    }

    if (!file_exists($projectsDir)) {
        mkdir($projectsDir, 0777, true);
    }

    $iconFileName = "icon-" . $projectNameFolder . ".png";
    $iconPath = $iconsDir . $iconFileName;
    $iconUrl  = $baseUrl . "assets/img/projects-icons/" . $iconFileName;

    if (isset($_FILES['projectIcon']) && $_FILES['projectIcon']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['projectIcon']['tmp_name'];
        $check = getimagesize($tmpName);

        if ($check !== false) {
            $ext = strtolower(pathinfo($_FILES['projectIcon']['name'], PATHINFO_EXTENSION));

            switch ($ext) {
                case 'jpg':
                case 'jpeg':
                    $srcImg = imagecreatefromjpeg($tmpName);
                    break;
                case 'png':
                    $srcImg = imagecreatefrompng($tmpName);
                    break;
                case 'gif':
                    $srcImg = imagecreatefromgif($tmpName);
                    break;
                default:
                    $srcImg = null;
                    break;
            }

            if ($srcImg) {
                imagepng($srcImg, $iconPath);
                imagedestroy($srcImg);
            } else {
                $iconUrl = $baseUrl . "assets/img/projects-icons/" . $defaultIcon;
            }
        } else {
            $iconUrl = $baseUrl . "assets/img/projects-icons/" . $defaultIcon;
        }
    } else {
        $iconUrl = $baseUrl . "assets/img/projects-icons/" . $defaultIcon;
    }

    $projectUrl = $baseUrl . "projects/" . $projectNameFolder;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO projects_address (project_name, project_path, project_icon) 
            VALUES (:name, :path, :icon)
        ");
        $stmt->execute([
            ':name' => $projectName,
            ':path' => $projectUrl,
            ':icon' => $iconUrl
        ]);

        header("Location: index.php?success=1");
        exit;

    } catch (PDOException $e) {
        die("Error saving project: " . $e->getMessage());
    }
}

if(isset($_GET['error']) && $_GET['error'] == "ProjectAlreadyExists") {
    echo '<script> alert("This Project Already Exists."); location.href ="./"</script>';
        
}
?>