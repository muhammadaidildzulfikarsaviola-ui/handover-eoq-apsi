<?php
$stmt = $pdo->query('SELECT a.created_at, a.analysis_name, i.item_code, i.name AS item_name, i.unit, a.annual_demand, a.ordering_cost, a.holding_cost, a.eoq_quantity, a.orders_per_year, a.order_interval_days, a.ordering_cost_total, a.holding_cost_total, a.relevant_total_cost, a.notes FROM eoq_analyses a JOIN items i ON i.id = a.item_id ORDER BY a.created_at DESC, a.id DESC');
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="laporan-eoq-' . date('Y-m-d') . '.csv"');
$output = fopen('php://output', 'w');
fwrite($output, "\xEF\xBB\xBF");
fputcsv($output, ['Tanggal', 'Nama Analisis', 'Kode Barang', 'Nama Barang', 'Satuan', 'Kebutuhan Tahunan (D)', 'Biaya Pesan (S)', 'Biaya Simpan (H)', 'EOQ', 'Order per Tahun', 'Interval Hari', 'Total Biaya Pesan', 'Total Biaya Simpan', 'Total Biaya Relevan', 'Catatan'], ';');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) fputcsv($output, array_values($row), ';');
fclose($output);
