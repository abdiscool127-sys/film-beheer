<?php
// Gemeenschappelijke navigatiebalk die in pagina's wordt opgenomen.
// Verwacht dat `$base` is ingesteld in de caller.
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-3 rounded">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= $base ?>/index.php?page=list">Filmbeheer</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="<?= $base ?>/index.php?page=list">Overzicht</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= $base ?>/index.php?page=create">Nieuwe film</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= $base ?>/index.php?page=genre">Zoek op genre</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
