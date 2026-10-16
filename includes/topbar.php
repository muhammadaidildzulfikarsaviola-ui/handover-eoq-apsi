<?php $flash = get_flash(); ?>
<div class="topbar">
    <div><div class="breadcrumbs">Workspace / <strong><?= h($pageTitle) ?></strong></div><h1><?= h($pageTitle) ?></h1></div>
    <div class="connection-state"><span class="state-dot <?= $pdo instanceof PDO ? 'connected' : '' ?>"></span><?= $pdo instanceof PDO ? 'Database terhubung' : 'Database belum terhubung' ?></div>
</div>
<?php if ($flash): ?><div class="notice notice-<?= h($flash['type']) ?>"><?= h($flash['message']) ?></div><?php endif; ?>
