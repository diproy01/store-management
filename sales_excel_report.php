<?php
require_once __DIR__ . '/init.php';

$from = $_GET['from_sales_date'] ?? null;
$to = $_GET['to_sales_date'] ?? null;
$start = is_string($from) ? strtotime($from . ' 00:00:00') : false;
$end = is_string($to) ? strtotime($to . ' 23:59:59') : false;
if (!$from || !$to || $start === false || $end === false || $start > $end) {
    http_response_code(400);
    exit('Enter a valid sales date range.');
}
$fromDate = date('Y-m-d H:i:s', $start);
$toDate = date('Y-m-d H:i:s', $end);
$result = $db->query("SELECT * FROM stock_sales WHERE count1 = 1 AND date BETWEEN '$fromDate' AND '$toDate'");
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
