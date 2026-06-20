<?php
// Pagina: Regisseurs - haalt data van api/directors.php via fetch
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../scripts/DirectorService.php';

// Bepaal basis-url voor links en API-calls. Dit zorgt dat de pagina
// zowel via index.php als direct via /pages werkt.

$script_dir = dirname($_SERVER['SCRIPT_NAME']);
if (basename($script_dir) === 'pages') {
  $base = dirname($script_dir);
} else {
  $base = $script_dir;
}
if (!empty($config->base_url)) {
  $base = rtrim($config->base_url, '/');
}

$service = new DirectorService();

// Handle POST create/update/delete
// Beschrijft de drie mogelijke acties: 'create', 'update' en 'delete'.
// Na elke POST doen we een redirect zodat browser refresh geen dubbele
// actie veroorzaakt (PRG patroon).
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
    header('Location: ' . $base . '/index.php?page=directors');
    exit;
  }
  if ($action === 'update' && !empty($_POST['id'])) {
    $id = (int)$_POST['id'];
    $service->update($id, [
      'naam' => $_POST['naam'] ?? null,
      'geboortedatum' => $_POST['geboortedatum'] ?? null,
      'bio' => $_POST['bio'] ?? null,
    ]);
    header('Location: ' . $base . '/index.php?page=directors');
    exit;
  }
  if ($action === 'delete' && !empty($_POST['id'])) {
    $service->delete((int)$_POST['id']);
    header('Location: ' . $base . '/index.php?page=directors');
    exit;
  }
}

// If editing, load existing director
$editDirector = null;
if (!empty($_GET['edit'])) {
  $editDirector = $service->get((int)$_GET['edit']);
}

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Filmbeheer - Regisseurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>/stylesheet/style.css">
</head>
<body>
<div class="container my-4">
    <?php include __DIR__ . '/_nav.php'; ?>

    <h1>Regisseurs</h1>
    <p>Deze pagina gebruikt een eigen database en ondersteunt CRUD (create, read, update, delete).</p>

    <div class="row mb-3">
      <div class="col-md-6">
        <?php if ($editDirector): ?>
        <!-- Edit-form: tonen wanneer gebruiker bewerkt -->
        <form method="post" class="card card-body">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?= e($editDirector['id']) ?>">
          <div class="mb-2">
            <input name="naam" value="<?= e($editDirector['naam']) ?>" class="form-control" placeholder="Naam" required>
          </div>
          <div class="mb-2">
            <input name="geboortedatum" type="date" value="<?= e($editDirector['geboortedatum']) ?>" class="form-control" placeholder="Geboortedatum">
          </div>
          <div class="mb-2">
            <textarea name="bio" class="form-control" placeholder="Biografie"><?= e($editDirector['bio']) ?></textarea>
          </div>
          <button class="btn btn-primary">Wijzig regisseur</button>
          <a class="btn btn-secondary ms-2" href="<?= $base ?>/index.php?page=directors">Annuleer</a>
        </form>
        <?php else: ?>
        <!-- Create-form: POST 'create' -->
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
          <button class="btn btn-primary">Voeg regisseur toe</button>
        </form>
        <?php endif; ?>
      </div>
      <div class="col-md-6">
        <input id="search" class="form-control" placeholder="Zoek regisseur..." />
      </div>
    </div>

    <div id="list" class="list-group"></div>

</div>
<script>
const base = '<?= $base ?>';
async function load(q=''){
  const url = `${base}/api/directors.php${q?('?s='+encodeURIComponent(q)):''}`;
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
          <a class="btn btn-sm btn-outline-primary" href="${base}/index.php?page=directors&edit=${a.id}">Bewerk</a>
          <form method="post" action="${base}/index.php?page=directors" style="display:inline">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${a.id}">
            <button class="btn btn-sm btn-outline-danger ms-1" onclick="return confirm('Weet je het zeker?')">Verwijder</button>
          </form>
        </div>
      `;
      list.appendChild(el);
    })
  } else {
    list.innerHTML = '<div class="alert alert-secondary">Geen regisseurs gevonden</div>';
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
