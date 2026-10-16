<?php
$stats = ['items' => 0, 'analyses' => 0, 'total_cost' => 0.0, 'avg_orders' => 0.0];
$recent = [];
$chartRows = [];
if ($pdo instanceof PDO) {
    $stats['items'] = (int) $pdo->query('SELECT COUNT(*) FROM items WHERE is_active = 1')->fetchColumn();
    $stats['analyses'] = (int) $pdo->query('SELECT COUNT(*) FROM eoq_analyses')->fetchColumn();
    $stats['total_cost'] = (float) $pdo->query('SELECT COALESCE(SUM(relevant_total_cost), 0) FROM eoq_analyses')->fetchColumn();
    $stats['avg_orders'] = (float) $pdo->query('SELECT COALESCE(AVG(orders_per_year), 0) FROM eoq_analyses')->fetchColumn();
    $recent = $pdo->query('SELECT a.id, a.analysis_name, i.name AS item_name, i.unit, a.eoq_quantity, a.relevant_total_cost, a.created_at FROM eoq_analyses a JOIN items i ON i.id = a.item_id ORDER BY a.created_at DESC, a.id DESC LIMIT 6')->fetchAll();
    $chartRows = $pdo->query('SELECT DATE_FORMAT(created_at, "%Y-%m") AS month_key, SUM(ordering_cost_total) AS ordering_cost, SUM(holding_cost_total) AS holding_cost FROM eoq_analyses WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 5 MONTH) GROUP BY DATE_FORMAT(created_at, "%Y-%m") ORDER BY month_key')->fetchAll();
}
?>
<section class="page-intro"><div><p class="eyebrow">RINGKASAN SISTEM</p><h2>Dashboard</h2><p>Ringkasan data barang dan hasil analisis EOQ yang tersimpan.</p></div><a class="button button-primary" href="index.php?page=calculator">+ Analisis baru</a></section>
<section class="stats-grid">
    <article class="stat-card"><span>Barang aktif</span><strong><?= number_format($stats['items'], 0, ',', '.') ?></strong><small>Data barang terdaftar</small></article>
    <article class="stat-card"><span>Analisis tersimpan</span><strong><?= number_format($stats['analyses'], 0, ',', '.') ?></strong><small>Seluruh riwayat analisis</small></article>
    <article class="stat-card"><span>Total biaya relevan</span><strong class="currency"><?= h(rupiah($stats['total_cost'])) ?></strong><small>Akumulasi analisis tersimpan</small></article>
    <article class="stat-card"><span>Rata-rata order</span><strong><?= number_id($stats['avg_orders'], 1) ?><small class="unit"> kali/tahun</small></strong><small>Rata-rata per analisis</small></article>
</section>
<section class="two-column">
    <article class="panel"><div class="panel-heading"><div><h3>Biaya pemesanan dan penyimpanan</h3><p>Rekap per bulan dari analisis tersimpan</p></div></div>
        <?php if ($chartRows): ?><div class="chart-box"><canvas id="costChart"></canvas></div><script>window.eoqChartRows = <?= json_encode($chartRows, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;</script>
        <?php else: ?><div class="empty-state"><strong>Belum ada data grafik</strong><p>Grafik akan muncul setelah analisis EOQ pertama disimpan.</p><a href="index.php?page=calculator">Buat analisis pertama</a></div><?php endif; ?>
    </article>
    <article class="panel"><div class="panel-heading"><div><h3>Akses cepat</h3><p>Menu yang sering digunakan</p></div></div>
        <div class="quick-links"><a href="index.php?page=items"><strong>Data Barang</strong><span>Tambah atau nonaktifkan barang →</span></a><a href="index.php?page=calculator"><strong>Analisis EOQ</strong><span>Hitung dan simpan hasil →</span></a><a href="index.php?page=history"><strong>Riwayat Analisis</strong><span>Lihat hasil sebelumnya →</span></a><a href="index.php?page=reports"><strong>Laporan</strong><span>Rekap dan ekspor CSV →</span></a></div>
    </article>
</section>
<section class="panel"><div class="panel-heading"><div><h3>Analisis terbaru</h3><p>Enam perhitungan terakhir</p></div><a href="index.php?page=history">Lihat semua →</a></div>
<?php if ($recent): ?><div class="table-wrap"><table><thead><tr><th>Nama analisis</th><th>Barang</th><th>EOQ</th><th>Biaya relevan</th><th>Tanggal</th></tr></thead><tbody><?php foreach ($recent as $row): ?><tr><td><?= h($row['analysis_name'] ?: 'Analisis #' . $row['id']) ?></td><td><?= h($row['item_name']) ?></td><td><?= number_id($row['eoq_quantity']) ?> <?= h($row['unit']) ?></td><td><?= h(rupiah($row['relevant_total_cost'])) ?></td><td><?= h(date('d-m-Y', strtotime($row['created_at']))) ?></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><div class="empty-state compact"><strong>Belum ada analisis tersimpan.</strong><p>Angka di dashboard tetap nol sampai data nyata dimasukkan.</p></div><?php endif; ?>
</section>
