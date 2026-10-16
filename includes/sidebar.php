<?php
$navigation = ['dashboard' => 'Dashboard', 'items' => 'Data Barang', 'calculator' => 'Analisis EOQ', 'history' => 'Riwayat Analisis', 'reports' => 'Laporan', 'guide' => 'Panduan EOQ', 'members' => 'Anggota Kelompok'];
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-heading">MENU UTAMA</div>
    <nav>
        <?php foreach ($navigation as $key => $label): ?>
            <a class="<?= $page === $key ? 'active' : '' ?>" href="index.php?page=<?= h($key) ?>" <?= $page === $key ? 'aria-current="page"' : '' ?>><?= h($label) ?></a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-bottom"><strong>APSI · EOQ</strong><span>Handover Asisten Praktikum</span></div>
</aside>
