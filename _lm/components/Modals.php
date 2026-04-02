<?php
/* ============================================================
   MyLocalHost — Modals Component
   ============================================================ */

require_once __DIR__ . '/../assets/icons/Icons.php';

function renderModals(): void {
?>

<!-- ─── Edit Modal ─── -->
<div class="modal-overlay" id="modal-edit" role="dialog" aria-modal="true" aria-labelledby="modal-edit-title">
  <div class="modal">
    <h2 class="modal__title" id="modal-edit-title">Editar Proyecto</h2>
    <input
      type="text"
      id="edit-project-input"
      class="input-field input-field--light"
      placeholder="Nombre del proyecto"
      maxlength="64"
      autocomplete="off"
      spellcheck="false"
    />
    <div class="modal__buttons">
      <button class="btn btn--neutral" id="btn-edit-save">
        <?= Icons::get('check', 15) ?>
        Guardar
      </button>
      <button class="btn btn--cancel" id="btn-edit-cancel">
        <?= Icons::get('x', 15) ?>
        Cancelar
      </button>
    </div>
  </div>
</div>

<!-- ─── Delete Modal ─── -->
<div class="modal-overlay" id="modal-delete" role="dialog" aria-modal="true" aria-labelledby="modal-delete-title">
  <div class="modal">
    <h2 class="modal__title" id="modal-delete-title">Eliminar Proyecto</h2>
    <p class="modal__subtitle" id="delete-confirm-text">¿Estás seguro de eliminar el proyecto?</p>
    <div class="modal__buttons">
      <button class="btn btn--danger" id="btn-delete-confirm">
        <?= Icons::get('trash', 15) ?>
        Eliminar
      </button>
      <button class="btn btn--neutral" id="btn-delete-cancel">
        <?= Icons::get('x', 15) ?>
        Cancelar
      </button>
    </div>
  </div>
</div>

<?php
}