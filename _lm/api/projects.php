<?php
/* ============================================================
   MyLocalHost — Projects API
   Handles: create, rename, delete
   ============================================================ */

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

// ─── Config ────────────────────────────────────────────────

// Folders to always ignore (will never appear as projects)
define('IGNORED_DIRS', [
  '_lm',           // This app's own folder
  'phpmyadmin',
  'phpMyAdmin',
  'PhpMyAdmin',
  'PHPMYADMIN',
  '.git',
  '.svn',
  'node_modules',
  'vendor',
]);

define('ROOT_PATH', realpath(__DIR__ . '/../../'));   // www / htdocs root

// ─── Helpers ───────────────────────────────────────────────

function jsonResponse(bool $ok, string $message, array $data = []): void {
  echo json_encode(array_merge(['ok' => $ok, 'message' => $message], $data));
  exit;
}

function validateName(string $name): ?string {
  $name = trim($name);
  if ($name === '') return 'El nombre no puede estar vacío.';
  if (strlen($name) > 64) return 'El nombre es demasiado largo (máx. 64 caracteres).';
  // Only allow safe folder name characters
  if (!preg_match('/^[\w\-. ]+$/u', $name)) {
    return 'El nombre contiene caracteres no permitidos.';
  }
  if (in_array(strtolower($name), array_map('strtolower', IGNORED_DIRS))) {
    return 'Ese nombre está reservado por el sistema.';
  }
  return null;
}

function getProjectPath(string $name): string {
  return ROOT_PATH . DIRECTORY_SEPARATOR . $name;
}

// ─── Router ────────────────────────────────────────────────

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? '';

// GET /api/projects.php?action=list
if ($method === 'GET' && $action === 'list') {
  $dirs = [];
  $items = scandir(ROOT_PATH);
  foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;
    if (!is_dir(ROOT_PATH . DIRECTORY_SEPARATOR . $item)) continue;
    if (in_array($item, IGNORED_DIRS)) continue;
    if (str_starts_with($item, '.')) continue;
    $dirs[] = $item;
  }
  sort($dirs, SORT_NATURAL | SORT_FLAG_CASE);
  jsonResponse(true, 'ok', ['projects' => $dirs]);
}

// POST actions require JSON body
$input = [];
if ($method === 'POST') {
  $raw = file_get_contents('php://input');
  $input = json_decode($raw, true) ?? [];
}

// POST /api/projects.php?action=create
if ($method === 'POST' && $action === 'create') {
  $name = $input['name'] ?? '';
  if ($err = validateName($name)) jsonResponse(false, $err);

  $path = getProjectPath($name);
  if (file_exists($path)) {
    jsonResponse(false, "Ya existe un proyecto llamado \"{$name}\".");
  }
  if (!mkdir($path, 0755, true)) {
    jsonResponse(false, 'No se pudo crear la carpeta. Verifica los permisos.');
  }
  // Create a minimal index.html so the folder is immediately accessible
  file_put_contents($path . '/index.html', "<!DOCTYPE html>\n<html lang=\"es\">\n<head><meta charset=\"UTF-8\"><title>{$name}</title></head>\n<body><h1>{$name}</h1></body>\n</html>");
  jsonResponse(true, "Proyecto \"{$name}\" creado.", ['name' => $name]);
}

// POST /api/projects.php?action=rename
if ($method === 'POST' && $action === 'rename') {
  $oldName = $input['old'] ?? '';
  $newName = $input['new'] ?? '';

  if ($err = validateName($newName)) jsonResponse(false, $err);

  $oldPath = getProjectPath($oldName);
  $newPath = getProjectPath($newName);

  if (!is_dir($oldPath)) jsonResponse(false, "El proyecto \"{$oldName}\" no existe.");
  if (file_exists($newPath)) jsonResponse(false, "Ya existe un proyecto llamado \"{$newName}\".");

  if (!rename($oldPath, $newPath)) {
    jsonResponse(false, 'No se pudo renombrar la carpeta. Verifica los permisos.');
  }
  jsonResponse(true, "Proyecto renombrado a \"{$newName}\".", ['old' => $oldName, 'new' => $newName]);
}

// POST /api/projects.php?action=delete
if ($method === 'POST' && $action === 'delete') {
  $name = $input['name'] ?? '';
  $path = getProjectPath($name);

  if (!is_dir($path)) jsonResponse(false, "El proyecto \"{$name}\" no existe.");

  // Recursive delete
  function rrmdir(string $dir): bool {
    foreach (scandir($dir) as $item) {
      if ($item === '.' || $item === '..') continue;
      $target = $dir . DIRECTORY_SEPARATOR . $item;
      is_dir($target) ? rrmdir($target) : unlink($target);
    }
    return rmdir($dir);
  }

  if (!rrmdir($path)) {
    jsonResponse(false, 'No se pudo eliminar la carpeta. Verifica los permisos.');
  }
  jsonResponse(true, "Proyecto \"{$name}\" eliminado.", ['name' => $name]);
}

jsonResponse(false, 'Acción no reconocida.');