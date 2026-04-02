<?php
/* ============================================================
   MyLocalHost — Main Entry Point
   Place this file at: www/_lm/index.php  OR  htdocs/_lm/index.php
   The root index.php redirects here automatically.
   ============================================================ */

require_once __DIR__ . '/assets/icons/Icons.php';
require_once __DIR__ . '/components/Topbar.php';
require_once __DIR__ . '/components/ProjectCard.php';
require_once __DIR__ . '/components/Modals.php';
require_once __DIR__ . '/components/Fab.php';
require_once __DIR__ . '/components/Notifications.php';

// ─── Get projects for initial server-side render ─────────────

$rootPath = realpath(__DIR__ . '/../../');

$ignoredDirs = [
  '_lm', 'phpmyadmin', 'phpMyAdmin', 'PhpMyAdmin', 'PHPMYADMIN',
  '.git', '.svn', 'node_modules', 'vendor',
];

$projects = [];
if ($rootPath && is_dir($rootPath)) {
  $items = scandir($rootPath);
  foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;
    if (!is_dir($rootPath . DIRECTORY_SEPARATOR . $item)) continue;
    if (in_array($item, $ignoredDirs)) continue;
    if (str_starts_with($item, '.')) continue;
    $projects[] = $item;
  }
  sort($projects, SORT_NATURAL | SORT_FLAG_CASE);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Gestor de proyectos localhost" />
  <title>MyLocalHost</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%231f2d2b' stroke-width='2'><path d='M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z'/></svg>" />
  <link rel="stylesheet" href="/_lm/styles/base.css" />
  <link rel="stylesheet" href="/_lm/styles/main.css" />
</head>
<body>

<div class="app-wrapper">

  <?php renderTopbar(); ?>

  <main class="main-content">
    <div class="content-container">

      <!-- Project cards rendered server-side for instant display -->
      <div id="project-list">
        <?php foreach ($projects as $i => $name): ?>
          <?php renderProjectCard($name, $i * 50); ?>
        <?php endforeach; ?>
      </div>

      <!-- Empty state (hidden by default if projects exist) -->
      <div
        id="empty-state"
        class="empty-state"
        style="display: <?= count($projects) === 0 ? 'flex' : 'none' ?>;"
      >
        <?= Icons::get('empty-folders', 64, 'empty-state__icon') ?>
        <p class="empty-state__title">Sin proyectos todavía</p>
        <p class="empty-state__text">Presiona el botón <strong>+</strong> para crear tu primer proyecto.</p>
      </div>

    </div>
  </main>

</div>

<?php renderModals(); ?>
<?php renderFab(); ?>
<?php renderNotifications(); ?>

<script src="/_lm/assets/app.js"></script>
</body>
</html>