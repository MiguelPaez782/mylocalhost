<?php
/* ============================================================
   MyLocalHost — FAB + Mini Form Component
   ============================================================ */

require_once __DIR__ . '/../assets/icons/Icons.php';

function renderFab(): void {
?>
<!-- ─── Mini New-Project Form ─── -->
<div class="mini-form" id="mini-form" role="dialog" aria-label="Nuevo proyecto">
  <p class="mini-form__title">Nuevo Proyecto</p>
  <input
    type="text"
    id="new-project-input"
    class="input-field"
    placeholder="Nombre del Proyecto"
    maxlength="64"
    autocomplete="off"
    spellcheck="false"
  />
  <div class="mini-form__buttons">
    <button class="btn btn--save" id="btn-create-save">Guardar</button>
  </div>
</div>

<!-- ─── FAB Button ─── -->
<button class="fab" id="fab-btn" aria-label="Agregar nuevo proyecto" title="Nuevo Proyecto">
  <span class="fab__icon"><?= Icons::get('plus', 26) ?></span>
</button>

<?php
}