<?php
// Pagina: Acteurs - haalt data van api/actors.php via fetch
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../scripts/ActorService.php';

// Verkrijg basis-url en set-up:
// We bepalen hier 'base' zodat links en fetch-requests correct werken
// ongeacht of de pagina via index.php of direct wordt opgevraagd.

$script_dir = dirname($_SERVER['SCRIPT_NAME']);
if (basename($script_dir) === 'pages') {
  $base = dirname($script_dir);
} else {
  $base = $script_dir;
}
if (!empty($config->base_url)) {
  $base = rtrim($config->base_url, '/');
}

$service = new ActorService();

// Handle form submissions: create, update, delete
// POST-actie 'create'/'update'/'delete' wordt hieronder behandeld.
// We gebruiken server-side redirects (Post-Redirect-Get) om dubbele
// submits te voorkomen en de gebruiker terug naar de lijst te sturen.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'create') {
    $naam = trim($_POST['naam'] ?? '');
    if ($naam !== '') {
      $service->create([
        'naam' => $naam,
        'geboortedatum' => $_POST['geboortedatum'] ?? null,
        'bio' => $_POST['bio'] ?? null,
      ]);
    }
    header('Location: ' . $base . '/index.php?page=actors');
    exit;
  }
  if ($action === 'update' && !empty($_POST['id'])) {
    $id = (int)$_POST['id'];
    $service->update($id, [
      'naam' => $_POST['naam'] ?? null,
      'geboortedatum' => $_POST['geboortedatum'] ?? null,
      'bio' => $_POST['bio'] ?? null,
    ]);
    header('Location: ' . $base . '/index.php?page=actors');
    exit;
  }
  if ($action === 'delete' && !empty($_POST['id'])) {
    $service->delete((int)$_POST['id']);
    header('Location: ' . $base . '/index.php?page=actors');
    exit;
  }
}

// If editing, load existing actor
$editActor = null;
if (!empty($_GET['edit'])) {
  $editActor = $service->get((int)$_GET['edit']);
}

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Filmbeheer - Acteurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>/stylesheet/style.css">
</head>
<body>
<div class="container my-4">
    <?php include __DIR__ . '/_nav.php'; ?>

    <h1>Acteurs</h1>
    <p>Deze pagina gebruikt een eigen database en ondersteunt CRUD (create, read, update, delete).</p>

    <div class="row mb-3">
      <div class="col-md-6">
        <?php if ($editActor): ?>
        <!-- Edit-form: wanneer gebruiker op 'Bewerk' klikt wordt deze vorm getoond -->
        <form method="post" class="card card-body">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?= e($editActor['id']) ?>">
          <div class="mb-2">
            <input name="naam" value="<?= e($editActor['naam']) ?>" class="form-control" placeholder="Naam" required>
          </div>
          <div class="mb-2">
            <input name="geboortedatum" type="date" value="<?= e($editActor['geboortedatum']) ?>" class="form-control" placeholder="Geboortedatum">
          </div>
          <div class="mb-2">
            <textarea name="bio" class="form-control" placeholder="Biografie"><?= e($editActor['bio']) ?></textarea>
          </div>
          <button class="btn btn-primary">Wijzig acteur</button>
          <a class="btn btn-secondary ms-2" href="<?= $base ?>/index.php?page=actors">Annuleer</a>
        </form>
           <?php else: ?>
           <!-- Create-form: gebruikt POST action 'create'. Server-side validatie
             controleert minimaal dat 'naam' niet leeg is. -->
           <form method="post" class="card card-body">
          <input type="hidden" name="action" value="create">
          <div class="mb-2">
            <input name="naam" class="form-control" placeholder="Naam" required>
          </div>
          <div class="mb-2">
            <input name="geboortedatum" type="date" class="form-control" placeholder="Geboortedatum">
          </div>
          <div class="mb-2">
            <textarea name="bio" class="form-control" placeholder="Biografie"></textarea>
          </div>
          <button class="btn btn-primary">Voeg acteur toe</button>
        </form>
        <?php endif; ?>
      </div>
      <div class="col-md-6">
        <input id="search" class="form-control" placeholder="Zoek acteur..." />
      </div>
    </div>

    <div id="list" class="list-group"></div>

</div>
<script>
const base = '<?= $base ?>';
async function load(q=''){
  const url = `${base}/api/actors.php${q?('?s='+encodeURIComponent(q)):''}`;
  const res = await fetch(url);
  const json = await res.json();
  const list = document.getElementById('list');
  list.innerHTML = '';
  if(json.data && json.data.length){
    json.data.forEach(a=>{
      const el = document.createElement('div');
      el.className = 'list-group-item';
      el.innerHTML = `
        <div class="d-flex w-100 justify-content-between">
          <h5 class="mb-1">${a.naam}</h5>
          <small>#${a.id}</small>
        </div>
        <p class="mb-1">${a.bio||''}</p>
        <small>${a.geboortedatum||''}</small>
        <div class="mt-2">
          <a class="btn btn-sm btn-outline-primary" href="${base}/index.php?page=actors&edit=${a.id}">Bewerk</a>
          <form method="post" action="${base}/index.php?page=actors" style="display:inline">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${a.id}">
            <button class="btn btn-sm btn-outline-danger ms-1" onclick="return confirm('Weet je het zeker?')">Verwijder</button>
          </form>
        </div>
      `;
      list.appendChild(el);
    })
  } else {
    list.innerHTML = '<div class="alert alert-secondary">Geen acteurs gevonden</div>';
  }
}

document.getElementById('search').addEventListener('input', e=>{
  load(e.target.value);
});

load();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
