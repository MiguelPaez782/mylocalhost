/* ============================================================
   MyLocalHost — Main Application Script
   Handles: CRUD, modals, FAB, notifications, animations
   ============================================================ */

'use strict';

// ─── Config ─────────────────────────────────────────────────

const API_BASE = '/_lm/api/projects.php';

// ─── State ──────────────────────────────────────────────────

let activeProject = null;   // Used by edit & delete modals

// ─── DOM References ─────────────────────────────────────────

const $ = id => document.getElementById(id);

const projectList    = $('project-list');
const emptyState     = $('empty-state');
const projectSearch  = $('project-search');

// FAB & mini form
const fabBtn         = $('fab-btn');
const miniForm       = $('mini-form');
const newProjectInput = $('new-project-input');
const btnCreateSave  = $('btn-create-save');

// Edit modal
const modalEdit      = $('modal-edit');
const editInput      = $('edit-project-input');
const btnEditSave    = $('btn-edit-save');
const btnEditCancel  = $('btn-edit-cancel');

// Delete modal
const modalDelete    = $('modal-delete');
const deleteText     = $('delete-confirm-text');
const btnDeleteConfirm = $('btn-delete-confirm');
const btnDeleteCancel  = $('btn-delete-cancel');

// Notifications
const notifContainer = $('notif-container');

// ─── Notification System ─────────────────────────────────────

function notify(message, type = 'success', duration = 3500) {
  const icons = {
    success: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`,
    error:   `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`,
    info:    `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>`,
  };

  const el = document.createElement('div');
  el.className = `notif notif--${type}`;
  el.innerHTML = `${icons[type] || ''}<span>${escapeHtml(message)}</span>`;
  notifContainer.appendChild(el);

  const remove = () => {
    el.classList.add('is-hiding');
    el.addEventListener('animationend', () => el.remove(), { once: true });
  };

  const timer = setTimeout(remove, duration);
  el.addEventListener('click', () => { clearTimeout(timer); remove(); });
}

// ─── API Calls ───────────────────────────────────────────────

async function api(action, body = null) {
  const opts = { headers: { 'Content-Type': 'application/json' } };
  if (body) {
    opts.method = 'POST';
    opts.body   = JSON.stringify(body);
  }
  const res  = await fetch(`${API_BASE}?action=${action}`, opts);
  const data = await res.json();
  return data;
}

// ─── Project List ─────────────────────────────────────────────

async function loadProjects() {
  projectList.innerHTML = '';
  // Show skeletons while loading
  for (let i = 0; i < 3; i++) {
    const sk = document.createElement('div');
    sk.className = 'skeleton';
    projectList.appendChild(sk);
  }

  const data = await api('list');
  projectList.innerHTML = '';

  if (!data.ok || data.projects.length === 0) {
    emptyState.style.display = 'flex';
    return;
  }
  emptyState.style.display = 'none';

  data.projects.forEach((name, i) => {
    projectList.appendChild(createCardElement(name, i * 50));
  });
}

function createCardElement(name, delay = 0) {
  const card = document.createElement('div');
  card.className = 'project-card';
  card.dataset.project = name;
  if (delay) card.style.animationDelay = `${delay}ms`;

  card.innerHTML = `
    <a href="/${encodeURIComponent(name)}/" class="project-card__link" title="Abrir ${escapeHtml(name)}">
      <svg class="project-card__icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
      <span class="project-card__name">${escapeHtml(name)}</span>
    </a>
    <div class="project-card__separator"></div>
    <div class="project-card__actions">
      <button class="btn-icon btn-icon--edit" data-action="edit" data-project="${escapeAttr(name)}" title="Editar proyecto" aria-label="Editar ${escapeHtml(name)}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      </button>
      <button class="btn-icon btn-icon--delete" data-action="delete" data-project="${escapeAttr(name)}" title="Eliminar proyecto" aria-label="Eliminar ${escapeHtml(name)}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
      </button>
    </div>`;

  return card;
}

function checkEmptyState() {
  const allCards = projectList.querySelectorAll('.project-card');
  const visibleCards = Array.from(allCards).filter(card => card.style.display !== 'none');

  if (allCards.length === 0) {
    emptyState.style.display = 'flex';
    emptyState.querySelector('.empty-state__title').textContent = 'Sin proyectos todavía';
    emptyState.querySelector('.empty-state__text').innerHTML = 'Presiona el botón <strong>+</strong> para crear tu primer proyecto.';
  } else if (visibleCards.length === 0) {
    emptyState.style.display = 'flex';
    emptyState.querySelector('.empty-state__title').textContent = 'No se encontraron proyectos';
    emptyState.querySelector('.empty-state__text').textContent = 'Intenta con otro término de búsqueda.';
  } else {
    emptyState.style.display = 'none';
  }
}

function filterProjects() {
  const query = projectSearch.value.toLowerCase().trim();
  const cards = projectList.querySelectorAll('.project-card');

  cards.forEach(card => {
    const name = card.dataset.project.toLowerCase();
    const matches = name.includes(query);
    card.style.display = matches ? 'flex' : 'none';
    
    // Disable entry animation if filtering to avoid flickering
    if (query) {
      card.style.animation = 'none';
      card.style.opacity = '1';
      card.style.transform = 'none';
    } else {
      // Restore animation if query is cleared (optional, might look jumpy)
      // card.style.animation = ''; 
    }
  });

  checkEmptyState();
}

projectSearch.addEventListener('input', filterProjects);

// ─── FAB & Mini Form ─────────────────────────────────────────

function openMiniForm() {
  miniForm.classList.add('is-visible');
  fabBtn.classList.add('is-open');
  newProjectInput.value = '';
  setTimeout(() => newProjectInput.focus(), 80);
}

function closeMiniForm() {
  miniForm.classList.remove('is-visible');
  fabBtn.classList.remove('is-open');
  newProjectInput.value = '';
}

fabBtn.addEventListener('click', () => {
  if (miniForm.classList.contains('is-visible')) {
    closeMiniForm();
  } else {
    openMiniForm();
  }
});

btnCreateSave.addEventListener('click', createProject);

newProjectInput.addEventListener('keydown', e => {
  if (e.key === 'Enter') createProject();
  if (e.key === 'Escape') closeMiniForm();
});

async function createProject() {
  const name = newProjectInput.value.trim();
  if (!name) { newProjectInput.focus(); return; }

  btnCreateSave.disabled = true;
  btnCreateSave.textContent = 'Creando…';

  const data = await api('create', { name });
  btnCreateSave.disabled = false;
  btnCreateSave.textContent = 'Guardar';

  if (!data.ok) { notify(data.message, 'error'); return; }

  closeMiniForm();
  const card = createCardElement(name, 0);
  projectList.insertBefore(card, projectList.firstChild);

  // Sort alphabetically after insert
  sortCards();
  filterProjects();
  notify(`Proyecto "${name}" creado exitosamente.`, 'success');
}

// ─── Edit Modal ───────────────────────────────────────────────

function openEditModal(projectName) {
  activeProject = projectName;
  editInput.value = projectName;
  openModal(modalEdit);
  setTimeout(() => {
    editInput.focus();
    editInput.select();
  }, 200);
}

btnEditSave.addEventListener('click', renameProject);

editInput.addEventListener('keydown', e => {
  if (e.key === 'Enter') renameProject();
  if (e.key === 'Escape') closeModal(modalEdit);
});

btnEditCancel.addEventListener('click', () => closeModal(modalEdit));

async function renameProject() {
  const newName = editInput.value.trim();
  if (!newName || newName === activeProject) { closeModal(modalEdit); return; }

  btnEditSave.disabled = true;

  const data = await api('rename', { old: activeProject, new: newName });
  btnEditSave.disabled = false;

  if (!data.ok) { notify(data.message, 'error'); return; }

  // Update DOM in-place
  const card = projectList.querySelector(`[data-project="${CSS.escape(activeProject)}"]`);
  if (card) {
    card.dataset.project = newName;
    card.querySelector('.project-card__name').textContent = newName;
    card.querySelector('.project-card__link').href = `/${encodeURIComponent(newName)}/`;
    card.querySelector('.project-card__link').title = `Abrir ${newName}`;
    card.querySelectorAll('[data-project]').forEach(el => el.dataset.project = newName);
  }

  sortCards();
  filterProjects();
  closeModal(modalEdit);
  notify(`Proyecto renombrado a "${newName}".`, 'info');
  activeProject = null;
}

// ─── Delete Modal ─────────────────────────────────────────────

function openDeleteModal(projectName) {
  activeProject = projectName;
  deleteText.textContent = `¿Estás seguro de eliminar "${projectName}"?`;
  openModal(modalDelete);
}

btnDeleteCancel.addEventListener('click', () => closeModal(modalDelete));

btnDeleteConfirm.addEventListener('click', async () => {
  if (!activeProject) return;
  btnDeleteConfirm.disabled = true;

  const data = await api('delete', { name: activeProject });
  btnDeleteConfirm.disabled = false;

  if (!data.ok) { notify(data.message, 'error'); return; }

  const card = projectList.querySelector(`[data-project="${CSS.escape(activeProject)}"]`);
  if (card) {
    card.classList.add('removing');
    card.addEventListener('animationend', () => {
      card.remove();
      checkEmptyState();
    }, { once: true });
  }

  closeModal(modalDelete);
  notify(`Proyecto "${activeProject}" eliminado.`, 'error');
  activeProject = null;
});

// ─── Modal Helpers ────────────────────────────────────────────

function openModal(overlay) {
  overlay.classList.add('is-visible');
  document.body.style.overflow = 'hidden';
}

function closeModal(overlay) {
  overlay.classList.remove('is-visible');
  document.body.style.overflow = '';
}

// Close modals on overlay click
[modalEdit, modalDelete].forEach(overlay => {
  overlay.addEventListener('click', e => {
    if (e.target === overlay) closeModal(overlay);
  });
});

// Keyboard: Escape closes active modal
document.addEventListener('keydown', e => {
  if (e.key !== 'Escape') return;
  if (modalEdit.classList.contains('is-visible')) closeModal(modalEdit);
  if (modalDelete.classList.contains('is-visible')) closeModal(modalDelete);
  if (miniForm.classList.contains('is-visible')) closeMiniForm();
});

// ─── Event Delegation (project list buttons) ──────────────────

projectList.addEventListener('click', e => {
  const btn = e.target.closest('[data-action]');
  if (!btn) return;
  e.preventDefault();
  const project = btn.dataset.project;
  if (btn.dataset.action === 'edit')   openEditModal(project);
  if (btn.dataset.action === 'delete') openDeleteModal(project);
});

// ─── Sort Helper ──────────────────────────────────────────────

function sortCards() {
  const cards = [...projectList.querySelectorAll('.project-card')];
  cards.sort((a, b) => a.dataset.project.localeCompare(b.dataset.project, undefined, { sensitivity: 'base' }));
  cards.forEach(c => projectList.appendChild(c));
}

// ─── Utilities ───────────────────────────────────────────────

function escapeHtml(str) {
  return str
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function escapeAttr(str) {
  return str.replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

// ─── Close mini form when clicking outside ───────────────────

document.addEventListener('click', e => {
  if (!miniForm.classList.contains('is-visible')) return;
  if (miniForm.contains(e.target) || fabBtn.contains(e.target)) return;
  closeMiniForm();
});

// ─── Init ────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', loadProjects);