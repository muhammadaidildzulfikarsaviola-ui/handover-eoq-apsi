<?php
declare(strict_types=1);

$page = isset($_GET['page']) && is_string($_GET['page']) ? $_GET['page'] : 'dashboard';
$allowedPages = ['dashboard', 'items', 'calculator', 'history', 'reports', 'guide'];
if (!in_array($page, $allowedPages, true)) {
    $page = 'dashboard';
}
$pageTitles = [
    'dashboard' => 'Dashboard',
    'items' => 'Data Barang',
    'calculator' => 'Analisis EOQ',
    'history' => 'Riwayat Analisis',
    'reports' => 'Laporan',
    'guide' => 'Panduan EOQ',
];
$title = $pageTitles[$page];

$stats = ['items' => 0, 'analyses' => 0, 'total_cost' => 0.0, 'avg_orders' => 0.0];
$recentAnalyses = [];
$chartRows = [];
$dbConnected = false;
$dbMessage = 'Database belum terhubung. Buat database dan import database/schema.sql.';

try {
    require __DIR__ . '/config/database.php';
    $dbConnected = true;
    $dbMessage = 'Data diambil langsung dari MySQL.';

    if ($page === 'dashboard') {
        $stats['items'] = (int) $pdo->query('SELECT COUNT(*) FROM items WHERE is_active = 1')->fetchColumn();
        $stats['analyses'] = (int) $pdo->query('SELECT COUNT(*) FROM eoq_analyses')->fetchColumn();
        $stats['total_cost'] = (float) $pdo->query('SELECT COALESCE(SUM(relevant_total_cost), 0) FROM eoq_analyses')->fetchColumn();
        $stats['avg_orders'] = (float) $pdo->query('SELECT COALESCE(AVG(orders_per_year), 0) FROM eoq_analyses')->fetchColumn();
        $recentAnalyses = $pdo->query(
            'SELECT a.id, a.analysis_name, i.name AS item_name, a.eoq_quantity, a.relevant_total_cost, a.created_at
             FROM eoq_analyses a JOIN items i ON i.id = a.item_id
             ORDER BY a.created_at DESC, a.id DESC LIMIT 6'
        )->fetchAll();
        $chartRows = $pdo->query(
            'SELECT DATE_FORMAT(created_at, "%Y-%m") AS month_key,
                    SUM(ordering_cost_total) AS ordering_cost,
                    SUM(holding_cost_total) AS holding_cost,
                    SUM(relevant_total_cost) AS total_cost
             FROM eoq_analyses
             WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 5 MONTH)
             GROUP BY DATE_FORMAT(created_at, "%Y-%m") ORDER BY month_key'
        )->fetchAll();
    }
} catch (Throwable $exception) {
    $dbConnected = false;
    $dbMessage = 'Database belum siap. Pastikan MySQL aktif dan database/schema.sql sudah diimpor.';
    error_log('Dashboard initialization error: ' . $exception->getMessage());
}

function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
function rupiah(float $value): string {
    return 'Rp' . number_format($value, 0, ',', '.');
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Dashboard analisis Economic Order Quantity untuk APSI.">
  <title><?= h($title) ?> · EOQ APSI</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="app-shell">
  <aside class="sidebar" id="sidebar">
    <a class="brand" href="index.php" aria-label="EOQ APSI dashboard">
      <span class="brand-mark">e<span>.</span></span>
      <span class="brand-copy"><strong>stockroom</strong><small>APSI · EOQ WORKSPACE</small></span>
    </a>
    <div class="nav-label">WORKSPACE</div>
    <nav class="nav-list" aria-label="Navigasi utama">
      <?php
      $nav = [
        'dashboard' => ['grid-2x2', 'Dashboard'],
        'items' => ['package', 'Data Barang'],
        'calculator' => ['calculator', 'Analisis EOQ'],
        'history' => ['history', 'Riwayat Analisis'],
        'reports' => ['chart-no-axes-combined', 'Laporan'],
      ];
      foreach ($nav as $key => [$icon, $label]): ?>
        <a class="nav-link <?= $page === $key ? 'active' : '' ?>" href="?page=<?= h($key) ?>" <?= $page === $key ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" data-icon="<?= h($icon) ?>"></span><span><?= h($label) ?></span>
          <?php if ($key === 'dashboard'): ?><span class="nav-dot"></span><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="nav-label nav-label-lower">REFERENCE</div>
    <a class="nav-link <?= $page === 'guide' ? 'active' : '' ?>" href="?page=guide">
      <span class="nav-icon" data-icon="book-open"></span><span>Panduan EOQ</span>
    </a>
    <div class="sidebar-bottom">
      <div class="sidebar-note"><span class="note-icon" data-icon="graduation-cap"></span><strong>Teaching workspace</strong><p>Ruang kerja analisis persediaan untuk pembelajaran APSI.</p><span class="version">FOUNDATION · V0.1</span></div>
      <div class="user-row"><div class="avatar">TA</div><div><strong>Asisten Praktikum</strong><small>EOQ workspace</small></div><span class="user-more" data-icon="ellipsis"></span></div>
    </div>
  </aside>

  <main class="main-content">
    <header class="topbar">
      <button class="icon-button mobile-menu" id="mobileMenu" aria-label="Buka navigasi"><span data-icon="menu"></span></button>
      <div class="breadcrumb"><span>Workspace</span><span class="crumb-slash">/</span><strong><?= h($title) ?></strong></div>
      <div class="topbar-actions"><span class="system-status"><i class="status-dot <?= $dbConnected ? 'online' : '' ?>"></i><?= $dbConnected ? 'Database connected' : 'Database not connected' ?></span><span class="topbar-divider"></span><button class="icon-button" aria-label="Bantuan" title="Panduan EOQ" onclick="location.href='?page=guide'"><span data-icon="circle-help"></span></button><div class="top-avatar">TA</div></div>
    </header>

    <div class="page-wrap">
      <?php if (!$dbConnected): ?><div class="notice notice-warning"><span data-icon="database"></span><div><strong>Database belum siap</strong><p><?= h($dbMessage) ?> Dashboard tetap bisa dibuka, tetapi angka statistik tidak akan direkayasa.</p></div></div><?php endif; ?>

      <?php if ($page === 'dashboard'): ?>
        <section class="page-heading"><div><div class="eyebrow">OVERVIEW <span>·</span> INVENTORY ANALYTICS</div><h1>Good day, <em>Asisten.</em></h1><p>Ini ringkasan kondisi data dan analisis persediaan kamu.</p></div><div class="heading-actions"><span class="date-chip"><span data-icon="calendar-days"></span><?= date('d M Y') ?></span><a class="button button-primary" href="?page=calculator"><span data-icon="plus"></span> Analisis baru</a></div></section>
        <section class="stats-grid" aria-label="Ringkasan statistik">
          <article class="stat-card"><div class="stat-top"><span>Total barang aktif</span><span class="stat-icon icon-mint" data-icon="package"></span></div><div class="stat-value"><?= number_format($stats['items'], 0, ',', '.') ?></div><div class="stat-foot"><span class="stat-sub">Item terdaftar di sistem</span></div></article>
          <article class="stat-card"><div class="stat-top"><span>Analisis tersimpan</span><span class="stat-icon icon-lilac" data-icon="calculator"></span></div><div class="stat-value"><?= number_format($stats['analyses'], 0, ',', '.') ?></div><div class="stat-foot"><span class="stat-sub">Seluruh riwayat perhitungan</span></div></article>
          <article class="stat-card"><div class="stat-top"><span>Total biaya relevan</span><span class="stat-icon icon-amber" data-icon="wallet"></span></div><div class="stat-value stat-currency"><?= h(rupiah($stats['total_cost'])) ?></div><div class="stat-foot"><span class="stat-sub">Akumulasi hasil analisis</span></div></article>
          <article class="stat-card"><div class="stat-top"><span>Rata-rata pemesanan</span><span class="stat-icon icon-blue" data-icon="calendar-clock"></span></div><div class="stat-value"><?= number_format($stats['avg_orders'], 1, ',', '.') ?><small class="value-unit">× / tahun</small></div><div class="stat-foot"><span class="stat-sub">Rata-rata antar analisis</span></div></article>
        </section>

        <section class="dashboard-grid">
          <article class="panel chart-panel"><div class="panel-heading"><div><h2>Cost overview</h2><p>Perbandingan biaya pemesanan dan penyimpanan</p></div><span class="period-chip">6 bulan terakhir</span></div>
            <?php if (count($chartRows) > 0): ?><div class="chart-legend"><span><i class="legend-order"></i>Biaya pemesanan</span><span><i class="legend-hold"></i>Biaya penyimpanan</span></div><div class="chart-wrap"><canvas id="costChart" aria-label="Grafik biaya EOQ enam bulan terakhir"></canvas></div>
            <?php else: ?><div class="empty-chart"><div class="empty-illustration"><span data-icon="chart-no-axes-combined"></span></div><strong>Belum ada tren untuk ditampilkan</strong><p>Grafik akan terbentuk otomatis setelah hasil analisis EOQ disimpan ke database.</p><a href="?page=calculator" class="text-link">Mulai analisis <span data-icon="arrow-up-right"></span></a></div><?php endif; ?>
          </article>
          <article class="panel quick-panel"><div class="panel-heading"><div><h2>Quick actions</h2><p>Jalan pintas pekerjaan</p></div><span class="panel-more" data-icon="ellipsis"></span></div>
            <a class="quick-action" href="?page=items"><span class="quick-icon quick-green" data-icon="package-plus"></span><span><strong>Tambah data barang</strong><small>Kelola master persediaan</small></span><span class="quick-arrow" data-icon="chevron-right"></span></a>
            <a class="quick-action" href="?page=calculator"><span class="quick-icon quick-purple" data-icon="calculator"></span><span><strong>Hitung EOQ</strong><small>Cari kuantitas optimal</small></span><span class="quick-arrow" data-icon="chevron-right"></span></a>
            <a class="quick-action" href="?page=history"><span class="quick-icon quick-orange" data-icon="history"></span><span><strong>Lihat riwayat</strong><small>Tinjau perhitungan sebelumnya</small></span><span class="quick-arrow" data-icon="chevron-right"></span></a>
            <a class="quick-action" href="?page=reports"><span class="quick-icon quick-blue" data-icon="file-chart-column"></span><span><strong>Buka laporan</strong><small>Ringkasan hasil analisis</small></span><span class="quick-arrow" data-icon="chevron-right"></span></a>
          </article>
        </section>

        <section class="panel recent-panel"><div class="panel-heading"><div><h2>Recent analyses</h2><p>Perhitungan terakhir yang tersimpan</p></div><a class="text-link" href="?page=history">Lihat semua <span data-icon="arrow-right"></span></a></div>
          <?php if ($recentAnalyses): ?><div class="table-scroll"><table><thead><tr><th>Barang / Analisis</th><th>Kuantitas EOQ</th><th>Biaya relevan</th><th>Tanggal</th></tr></thead><tbody><?php foreach ($recentAnalyses as $row): ?><tr><td><strong><?= h((string) ($row['analysis_name'] ?: $row['item_name'])) ?></strong><small><?= h((string) $row['item_name']) ?></small></td><td><?= number_format((float) $row['eoq_quantity'], 2, ',', '.') ?></td><td><?= h(rupiah((float) $row['relevant_total_cost'])) ?></td><td><?= h(date('d M Y', strtotime((string) $row['created_at']))) ?></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><div class="empty-table"><span class="empty-table-icon" data-icon="file-clock"></span><div><strong>Belum ada analisis tersimpan</strong><p>Hasil perhitungan yang disimpan nanti akan muncul di sini.</p></div><a class="button button-secondary button-small" href="?page=calculator">Buat analisis</a></div><?php endif; ?>
        </section>
        <footer class="page-footer"><span>EOQ APSI <span class="footer-dot">·</span> Inventory analytics workspace</span><span>Data diperbarui dari MySQL saat halaman dimuat.</span></footer>

      <?php else: ?>
        <section class="page-heading"><div><div class="eyebrow">WORKSPACE <span>·</span> EOQ APSI</div><h1><?= h($title) ?></h1><p><?= h(match ($page) { 'items' => 'Kelola master barang yang digunakan dalam analisis persediaan.', 'calculator' => 'Hitung kuantitas pemesanan ekonomis berdasarkan parameter persediaan.', 'history' => 'Tinjau kembali hasil perhitungan yang tersimpan.', 'reports' => 'Ringkasan dan keluaran laporan dari hasil analisis EOQ.', 'guide' => 'Referensi rumus, variabel, asumsi, dan interpretasi hasil EOQ.', default => '' }) ?></p></div><?php if ($page === 'calculator'): ?><a class="button button-secondary" href="?page=items"><span data-icon="package"></span> Data barang</a><?php endif; ?></section>
        <section class="panel module-panel"><div class="module-icon" data-icon="<?= h(match ($page) { 'items' => 'package', 'calculator' => 'calculator', 'history' => 'history', 'reports' => 'file-chart-column', default => 'book-open' }) ?>"></div><h2><?= h($title) ?></h2><p class="module-description"><?= h(match ($page) { 'items' => 'Modul data barang akan memuat tabel, pencarian, kategori, serta form tambah dan edit barang yang tersambung ke MySQL.', 'calculator' => 'Modul kalkulator akan memvalidasi D, S, dan H; menghitung EOQ; lalu menyimpan parameter dan hasil ke database.', 'history' => 'Modul ini akan menampilkan riwayat analisis dari database, lengkap dengan detail dan filter.', 'reports' => 'Modul laporan akan menampilkan rekap analisis serta opsi ekspor data.', default => 'Panduan memuat rumus EOQ, arti variabel, asumsi model, dan cara membaca hasil.' }) ?></p>
          <?php if ($page === 'guide'): ?><div class="formula-box"><span>Economic Order Quantity</span><strong>EOQ = √(2DS / H)</strong><p>D = permintaan tahunan · S = biaya setiap pemesanan · H = biaya simpan per unit per tahun.</p></div><?php elseif ($page === 'calculator'): ?><div class="formula-box"><span>Input utama</span><strong>D · S · H</strong><p>Semua nilai harus positif. Satuan waktu biaya penyimpanan harus konsisten dengan permintaan tahunan.</p></div><?php endif; ?><span class="module-tag"><i></i> Struktur database disiapkan · modul bertahap</span></section>
      <?php endif; ?>
    </div>
  </main>
</div>
<script>window.eoqChartRows = <?= json_encode($chartRows, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;</script>
<script src="assets/js/app.js"></script>
<?php if ($page === 'dashboard'): ?><script src="assets/js/dashboard.js"></script><?php endif; ?>
</body>
</html>
