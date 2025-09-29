<?php
require_once 'config.php';
require_once 'add_project.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/img/icon-app.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <title>MyLocalHost</title>
</head>
<body class="grid-content">
 
    <nav class="navbar">
        <a href="<?php echo $baseUrl; ?>" class="link"><img src="assets/img/icon-app.png" alt="icon-app" class="icon-app"><h1>MyLocalHost</h1></a>
        <div class="btn-container">
            <a href="<?php echo $phpMyAdminUrl; ?>" target="_blank" class="link">PhpMyAdmin</a>
        </div>
    </nav>

    <div class="main" id="projects-container">
        <?php require 'get_projects.php'; ?>
    </div>


    <div class="manager-projects-container">

        <div class="form-manager-project-container">
            <input type="search" name="searchProject" id="search_project" class="search" placeholder="Search Project">
        </div>
        
        <div class="form-manager-project-container">
            <h2 class="title-form-project">Add New Project</h2>
            <form class="form-add-new-project" action="#" method="post" enctype="multipart/form-data">
                <div class="input-field">
                    <label for="project_name">Project Name:</label>
                    <input type="text" name="projectName" id="project_name" required>
                </div>    
            
                <div class="input-field">
                    <label for="project_icon">Projecto Icon:</label>
                    <input type="file" name="projectIcon" id="project_icon" accept="image/*">
                </div>
                
                <input type="submit" name="createProject" value="Create Project" class="btn btn-bg-dark">
                <input type="reset" value="Clear" class="btn btn-bg-dark">
            </form>
        </div>
        
        <div class="form-manager-project-container">
            <h2 class="title-form-project">Delete Project</h2>
            <form action="delete_project.php" method="get" class="form-delete-project">
                <div class="input-field">
                    <label for="select_project_delete">Select Project</label>
                    <select name="selectProjectDelete" id="select_project_delete" required>
                        <option value="0">Select a Project</option>
                        <?php require_once 'delete_project.php';?>
                    </select>
                </div>
                <input type="submit" name="deleteProject" value="Delete" class="btn btn-bg-dark">
            </form>
        </div>
    </div>

    <script src="assets/js/search_ajax.js"></script>

</body>
</html>
