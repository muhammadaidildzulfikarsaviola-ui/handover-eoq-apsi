<?php
if (!($pdo instanceof PDO)) { echo '<section class="panel"><p>Hubungkan database untuk mengelola data barang.</p></section>'; return; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = post_string('action');
    try {
        if ($action === 'save_item') {
            $id = (int) ($_POST['id'] ?? 0);
            $code = post_string('item_code');
            $name = post_string('name');
            $unit = post_string('unit', 'unit');
            $description = post_string('description');
            if ($code === '' || $name === '' || $unit === '') throw new RuntimeException('Kode, nama, dan satuan barang wajib diisi.');
            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE items SET item_code = ?, name = ?, unit = ?, description = ? WHERE id = ?');
                $stmt->execute([$code, $name, $unit, $description !== '' ? $description : null, $id]);
                set_flash('success', 'Data barang berhasil diperbarui.');
            } else {
                $stmt = $pdo->prepare('INSERT INTO items (item_code, name, unit, description) VALUES (?, ?, ?, ?)');
                $stmt->execute([$code, $name, $unit, $description !== '' ? $description : null]);
                set_flash('success', 'Data barang berhasil ditambahkan.');
            }
            redirect_to('items');
        }
        if ($action === 'toggle_item') {
            $id = (int) ($_POST['id'] ?? 0);
            $stmt = $pdo->prepare('UPDATE items SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?');
            $stmt->execute([$id]);
            set_flash('success', 'Status barang berhasil diubah.');
            redirect_to('items');
        }
    } catch (PDOException $exception) {
        set_flash('error', $exception->getCode() === '23000' ? 'Kode barang sudah digunakan. Gunakan kode lain.' : 'Data barang gagal disimpan.');
        redirect_to('items');
    } catch (RuntimeException $exception) {
        set_flash('error', $exception->getMessage());
        redirect_to('items');
    }
}
$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM items WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $editItem = $stmt->fetch() ?: null;
}
$search = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';
$sql = 'SELECT * FROM items';
$params = [];
if ($search !== '') { $sql .= ' WHERE item_code LIKE ? OR name LIKE ?'; $params = ['%' . $search . '%', '%' . $search . '%']; }
$sql .= ' ORDER BY is_active DESC, name ASC';
$stmt = $pdo->prepare($sql); $stmt->execute($params); $items = $stmt->fetchAll();
?>
<section class="page-intro"><div><h2>Data Barang</h2><p>Tambahkan dan kelola master barang untuk perhitungan EOQ.</p></div></section>
<section class="panel form-panel"><h3><?= $editItem ? 'Edit barang' : 'Tambah barang' ?></h3>
<form method="post" class="form-grid">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="action" value="save_item"><input type="hidden" name="id" value="<?= h($editItem['id'] ?? 0) ?>">
    <label>Kode barang <input name="item_code" maxlength="50" required value="<?= h($editItem['item_code'] ?? '') ?>" placeholder="Contoh: BB-001"></label>
    <label>Nama barang <input name="name" maxlength="150" required value="<?= h($editItem['name'] ?? '') ?>" placeholder="Nama bahan baku"></label>
    <label>Satuan <input name="unit" maxlength="30" required value="<?= h($editItem['unit'] ?? 'kg') ?>" placeholder="kg, pcs, liter"></label>
    <label class="full-width">Keterangan <input name="description" value="<?= h($editItem['description'] ?? '') ?>" placeholder="Opsional"></label>
    <div class="form-actions"><button class="button button-primary" type="submit"><?= $editItem ? 'Simpan perubahan' : 'Tambah barang' ?></button><?php if ($editItem): ?><a class="button button-secondary" href="index.php?page=items">Batal</a><?php endif; ?></div>
</form></section>
<section class="panel"><div class="panel-heading"><div><h3>Daftar barang</h3><p><?= count($items) ?> barang ditemukan</p></div><form method="get" class="search-form"><input type="hidden" name="page" value="items"><input name="q" value="<?= h($search) ?>" placeholder="Cari kode atau nama"><button class="button button-secondary">Cari</button></form></div>
<?php if ($items): ?><div class="table-wrap"><table><thead><tr><th>Kode</th><th>Nama barang</th><th>Satuan</th><th>Status</th><th>Aksi</th></tr></thead><tbody><?php foreach ($items as $item): ?><tr><td><?= h($item['item_code']) ?></td><td><strong><?= h($item['name']) ?></strong><?php if ($item['description']): ?><small><?= h($item['description']) ?></small><?php endif; ?></td><td><?= h($item['unit']) ?></td><td><span class="status-pill <?= $item['is_active'] ? 'status-active' : 'status-inactive' ?>"><?= $item['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td><td class="actions"><a href="index.php?page=items&edit=<?= (int) $item['id'] ?>">Edit</a><form method="post" onsubmit="return confirm('Ubah status barang ini?')"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="action" value="toggle_item"><input type="hidden" name="id" value="<?= (int) $item['id'] ?>"><button type="submit" class="link-button"><?= $item['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?></button></form></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><div class="empty-state"><strong>Belum ada data barang.</strong><p>Isi form di atas untuk menambahkan barang pertama.</p></div><?php endif; ?>
</section>
