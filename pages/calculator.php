<?php
if (!($pdo instanceof PDO)) { echo '<section class="panel"><p>Hubungkan database untuk menghitung dan menyimpan analisis EOQ.</p></section>'; return; }
$activeItems = $pdo->query('SELECT id, item_code, name, unit FROM items WHERE is_active = 1 ORDER BY name')->fetchAll();
$errors = [];
$result = null;
$values = ['item_id' => '', 'analysis_name' => '', 'annual_demand' => '', 'ordering_cost' => '', 'holding_cost' => '', 'notes' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post_string('action') === 'calculate_eoq') {
    verify_csrf();
    foreach ($values as $key => $default) $values[$key] = post_string($key, $default);
    $itemId = (int) $values['item_id'];
    $d = filter_var($values['annual_demand'], FILTER_VALIDATE_FLOAT);
    $s = filter_var($values['ordering_cost'], FILTER_VALIDATE_FLOAT);
    $holding = filter_var($values['holding_cost'], FILTER_VALIDATE_FLOAT);
    if (!$itemId) $errors[] = 'Pilih barang terlebih dahulu.';
    if ($d === false || $d <= 0) $errors[] = 'Kebutuhan tahunan harus lebih besar dari nol.';
    if ($s === false || $s <= 0) $errors[] = 'Biaya pesan harus lebih besar dari nol.';
    if ($holding === false || $holding <= 0) $errors[] = 'Biaya simpan per unit per tahun harus lebih besar dari nol.';
    if (!$errors) {
        $itemCheck = $pdo->prepare('SELECT id FROM items WHERE id = ? AND is_active = 1');
        $itemCheck->execute([$itemId]);
        if (!$itemCheck->fetchColumn()) $errors[] = 'Barang yang dipilih tidak aktif atau tidak ditemukan.';
    }
    if (!$errors) {
        $q = sqrt((2 * (float) $d * (float) $s) / (float) $holding);
        $orders = (float) $d / $q;
        $interval = 365 / $orders;
        $orderingTotal = ((float) $d / $q) * (float) $s;
        $holdingTotal = ($q / 2) * (float) $holding;
        $total = $orderingTotal + $holdingTotal;
        $result = ['eoq' => $q, 'orders' => $orders, 'interval' => $interval, 'ordering' => $orderingTotal, 'holding' => $holdingTotal, 'total' => $total];
        try {
            $stmt = $pdo->prepare('INSERT INTO eoq_analyses (item_id, analysis_name, annual_demand, ordering_cost, holding_cost, eoq_quantity, orders_per_year, order_interval_days, ordering_cost_total, holding_cost_total, relevant_total_cost, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$itemId, $values['analysis_name'] !== '' ? $values['analysis_name'] : null, $d, $s, $holding, $q, $orders, $interval, $orderingTotal, $holdingTotal, $total, $values['notes'] !== '' ? $values['notes'] : null]);
            set_flash('success', 'Analisis berhasil dihitung dan disimpan ke database.');
            redirect_to('history', ['id' => (int) $pdo->lastInsertId()]);
        } catch (PDOException $exception) {
            error_log('EOQ save failed: ' . $exception->getMessage());
            $errors[] = 'Hasil berhasil dihitung, tetapi gagal disimpan. Periksa struktur database.';
        }
    }
}
?>
<section class="page-intro"><div><h2>Analisis EOQ</h2><p>Masukkan data permintaan dan biaya. Hasil akan dihitung dan disimpan ke database.</p></div></section>
<?php if (!$activeItems): ?><div class="notice notice-warning">Belum ada barang aktif. Tambahkan barang terlebih dahulu di menu <a href="index.php?page=items">Data Barang</a>.</div><?php endif; ?>
<?php if ($errors): ?><div class="notice notice-error"><strong>Periksa input:</strong><ul><?php foreach ($errors as $error): ?><li><?= h($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<section class="panel form-panel"><h3>Data perhitungan</h3>
<form method="post" class="form-grid">
<input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="action" value="calculate_eoq">
<label class="full-width">Barang <select name="item_id" required><option value="">Pilih barang</option><?php foreach ($activeItems as $item): ?><option value="<?= (int) $item['id'] ?>" <?= (string) $item['id'] === $values['item_id'] ? 'selected' : '' ?>><?= h($item['item_code'] . ' · ' . $item['name'] . ' (' . $item['unit'] . ')') ?></option><?php endforeach; ?></select></label>
<label>Nama analisis <span class="optional">(opsional)</span><input name="analysis_name" maxlength="150" value="<?= h($values['analysis_name']) ?>" placeholder="Contoh: EOQ bahan baku Oktober"></label>
<label>Kebutuhan tahunan (D)<input type="number" name="annual_demand" min="0.0001" step="any" required value="<?= h($values['annual_demand']) ?>" placeholder="Contoh: 12000"></label>
<label>Biaya pesan per order (S) <span class="optional">Rp</span><input type="number" name="ordering_cost" min="0.0001" step="any" required value="<?= h($values['ordering_cost']) ?>" placeholder="Contoh: 250000"></label>
<label>Biaya simpan per unit per tahun (H) <span class="optional">Rp</span><input type="number" name="holding_cost" min="0.0001" step="any" required value="<?= h($values['holding_cost']) ?>" placeholder="Contoh: 1000"></label>
<label class="full-width">Catatan <input name="notes" value="<?= h($values['notes']) ?>" placeholder="Asumsi atau sumber data (opsional)"></label>
<div class="form-actions"><button class="button button-primary" type="submit" <?= !$activeItems ? 'disabled' : '' ?>>Hitung dan simpan EOQ</button><a class="button button-secondary" href="index.php?page=guide">Lihat rumus</a></div>
</form></section>
<section class="panel"><h3>Rumus yang digunakan</h3><div class="formula-line">EOQ = √(2 × D × S / H)</div><p class="muted">Frekuensi order = D / EOQ. Biaya relevan = biaya pemesanan + biaya penyimpanan. H harus dinyatakan per unit per tahun.</p></section>
