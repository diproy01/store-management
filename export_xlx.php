<?php
require_once __DIR__ . '/init.php';

$result = $db->query("SELECT * FROM stock_sales");
header('Content-Type: text/tab-separated-values; charset=UTF-8');
header('Content-Disposition: attachment; filename="dipak.xls"');
header('Cache-Control: no-store');

$columns = array_column($result->fetch_fields(), 'name');
echo implode("\t", $columns), "\n";
while ($row = $result->fetch_row()) {
    $cells = array_map(
        static fn ($value) => str_replace(["\r", "\n", "\t"], ' ', (string) ($value ?? 'NULL')),
        $row
    );
    echo implode("\t", $cells), "\n";
}
