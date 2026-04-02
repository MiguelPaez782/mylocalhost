<?php
/* ============================================================
   MyLocalHost — Topbar Component
   ============================================================ */

require_once __DIR__ . '/../assets/icons/Icons.php';

function renderTopbar(): void {
?>
<header class="topbar">
  <h1 class="topbar__title">MyLocalHost</h1>
  <div class="topbar__search">
    <?= Icons::get('search', 16, 'topbar__search-icon') ?>
    <input
      type="text"
      id="project-search"
      class="topbar__search-input"
      placeholder="Buscar proyecto..."
      autocomplete="off"
    >
  </div>
  <nav class="topbar__actions">
    <a
      href="http://localhost/phpmyadmin"
      target="_blank"
      rel="noopener noreferrer"
      class="btn-phpmyadmin"
      title="Abrir phpMyAdmin"
    >
      <?= Icons::get('database', 18) ?>
      <span>phpMyAdmin</span>
    </a>
  </nav>
</header>
<?php
}