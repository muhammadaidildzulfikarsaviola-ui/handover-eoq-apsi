<?php
if (!($pdo instanceof PDO)) { echo '<section class="panel"><p>Hubungkan database untuk melihat riwayat analisis.</p></section>'; return; }
$detail = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT a.*, i.item_code, i.name AS item_name, i.unit FROM eoq_analyses a JOIN items i ON i.id = a.item_id WHERE a.id = ?');
    $stmt->execute([(int) $_GET['id']]); $detail = $stmt->fetch() ?: null;
}
$search = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';
$sql = 'SELECT a.id, a.analysis_name, a.annual_demand, a.eoq_quantity, a.orders_per_year, a.relevant_total_cost, a.created_at, i.item_code, i.name AS item_name, i.unit FROM eoq_analyses a JOIN items i ON i.id = a.item_id';
$params = [];
if ($search !== '') { $sql .= ' WHERE i.name LIKE ? OR i.item_code LIKE ? OR a.analysis_name LIKE ?'; $params = array_fill(0, 3, '%' . $search . '%'); }
$sql .= ' ORDER BY a.created_at DESC, a.id DESC';
$stmt = $pdo->prepare($sql); $stmt->execute($params); $analyses = $stmt->fetchAll();
?>
<section class="page-intro"><div><h2>Riwayat Analisis</h2><p>Semua hasil perhitungan EOQ yang sudah disimpan.</p></div><a class="button button-primary" href="index.php?page=calculator">+ Analisis baru</a></section>
<?php if ($detail): ?><section class="panel detail-panel"><div class="panel-heading"><div><h3><?= h($detail['analysis_name'] ?: 'Analisis #' . $detail['id']) ?></h3><p><?= h($detail['item_code'] . ' · ' . $detail['item_name']) ?> · <?= h(date('d-m-Y H:i', strtotime($detail['created_at']))) ?></p></div><a href="index.php?page=history" class="button button-secondary">Tutup detail</a></div>
<div class="result-grid"><div><span>EOQ optimal</span><strong><?= number_id($detail['eoq_quantity']) ?> <?= h($detail['unit']) ?></strong></div><div><span>Frekuensi order</span><strong><?= number_id($detail['orders_per_year']) ?> kali/tahun</strong></div><div><span>Interval order</span><strong><?= number_id($detail['order_interval_days']) ?> hari</strong></div><div><span>Total biaya relevan</span><strong><?= h(rupiah($detail['relevant_total_cost'])) ?></strong></div></div>
<table><tbody><tr><th>Kebutuhan tahunan (D)</th><td><?= number_id($detail['annual_demand']) ?> <?= h($detail['unit']) ?></td></tr><tr><th>Biaya pesan (S)</th><td><?= h(rupiah($detail['ordering_cost'])) ?></td></tr><tr><th>Biaya simpan per unit/tahun (H)</th><td><?= h(rupiah($detail['holding_cost'])) ?></td></tr><tr><th>Total biaya pemesanan</th><td><?= h(rupiah($detail['ordering_cost_total'])) ?></td></tr><tr><th>Total biaya penyimpanan</th><td><?= h(rupiah($detail['holding_cost_total'])) ?></td></tr><?php if ($detail['notes']): ?><tr><th>Catatan</th><td><?= h($detail['notes']) ?></td></tr><?php endif; ?></tbody></table></section><?php elseif (isset($_GET['id'])): ?><div class="notice notice-error">Analisis tidak ditemukan.</div><?php endif; ?>
<section class="panel"><div class="panel-heading"><div><h3>Daftar analisis</h3><p><?= count($analyses) ?> hasil ditemukan</p></div><form method="get" class="search-form"><input type="hidden" name="page" value="history"><input name="q" value="<?= h($search) ?>" placeholder="Cari nama atau kode barang"><button class="button button-secondary">Cari</button></form></div>
<?php if ($analyses): ?><div class="table-wrap"><table><thead><tr><th>Tanggal</th><th>Nama analisis</th><th>Barang</th><th>EOQ</th><th>Order/tahun</th><th>Total biaya relevan</th><th></th></tr></thead><tbody><?php foreach ($analyses as $row): ?><tr><td><?= h(date('d-m-Y', strtotime($row['created_at']))) ?></td><td><?= h($row['analysis_name'] ?: 'Analisis #' . $row['id']) ?></td><td><?= h($row['item_code'] . ' · ' . $row['item_name']) ?></td><td><?= number_id($row['eoq_quantity']) ?> <?= h($row['unit']) ?></td><td><?= number_id($row['orders_per_year']) ?></td><td><?= h(rupiah($row['relevant_total_cost'])) ?></td><td><a href="index.php?page=history&id=<?= (int) $row['id'] ?>">Detail</a></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><div class="empty-state"><strong>Belum ada riwayat analisis.</strong><p>Hitung EOQ dari menu Analisis EOQ untuk mengisi riwayat ini.</p></div><?php endif; ?>
</section>
