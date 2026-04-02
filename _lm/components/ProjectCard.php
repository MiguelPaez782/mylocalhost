<?php
/* ============================================================
   MyLocalHost — Project Card Component
   ============================================================ */

require_once __DIR__ . '/../assets/icons/Icons.php';

function renderProjectCard(string $name, int $delay = 0): void {
  $encodedName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
  $delayStyle  = $delay > 0 ? " style=\"animation-delay:{$delay}ms\"" : '';
?>
<div class="project-card" data-project="<?= $encodedName ?>"<?= $delayStyle ?>>
  <a
    href="/<?= rawurlencode($name) ?>/"
    class="project-card__link"
    title="Abrir <?= $encodedName ?>"
  >
    <?= Icons::get('folder', 22, 'project-card__icon') ?>
    <span class="project-card__name"><?= $encodedName ?></span>
  </a>
  <div class="project-card__separator"></div>
  <div class="project-card__actions">
    <button
      class="btn-icon btn-icon--edit"
      data-action="edit"
      data-project="<?= $encodedName ?>"
      title="Editar proyecto"
      aria-label="Editar <?= $encodedName ?>"
    >
      <?= Icons::get('edit', 16) ?>
    </button>
    <button
      class="btn-icon btn-icon--delete"
      data-action="delete"
      data-project="<?= $encodedName ?>"
      title="Eliminar proyecto"
      aria-label="Eliminar <?= $encodedName ?>"
    >
      <?= Icons::get('trash', 16) ?>
    </button>
  </div>
</div>
<?php
}