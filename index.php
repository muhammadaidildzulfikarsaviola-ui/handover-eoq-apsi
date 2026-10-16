<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/includes/functions.php';

$page = isset($_GET['page']) && is_string($_GET['page']) ? $_GET['page'] : 'dashboard';
$pages = [
    'dashboard' => ['Dashboard', 'pages/dashboard.php'],
    'items' => ['Data Barang', 'pages/items.php'],
    'calculator' => ['Analisis EOQ', 'pages/calculator.php'],
    'history' => ['Riwayat Analisis', 'pages/history.php'],
    'reports' => ['Laporan', 'pages/reports.php'],
    'guide' => ['Panduan EOQ', 'pages/guide.php'],
    'members' => ['Anggota Kelompok', 'pages/members.php'],
];
if (!isset($pages[$page])) $page = 'dashboard';
$pageTitle = $pages[$page][0];
$dbError = null;
$pdo = null;

try {
    require __DIR__ . '/config/database.php';
} catch (Throwable $exception) {
    error_log('Database connection error: ' . $exception->getMessage());
    $dbError = 'Database belum terhubung. Pastikan MySQL aktif dan database/schema.sql sudah diimpor.';
}

if (isset($_GET['export']) && $_GET['export'] === 'csv' && $page === 'reports' && $pdo instanceof PDO) {
    require __DIR__ . '/pages/export.php';
    exit;
}

require __DIR__ . '/includes/header.php';
?>
<div class="app-layout">
    <?php require __DIR__ . '/includes/sidebar.php'; ?>
    <div class="main-area">
        <?php require __DIR__ . '/includes/topbar.php'; ?>
        <main class="content">
            <?php if ($dbError !== null): ?><div class="notice notice-error"><?= h($dbError) ?></div><?php endif; ?>
            <?php require __DIR__ . '/' . $pages[$page][1]; ?>
        </main>
        <?php require __DIR__ . '/includes/footer.php'; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/app.js"></script>
<?php if ($page === 'dashboard'): ?><script src="assets/js/dashboard.js"></script><?php endif; ?>
</body>
</html>
