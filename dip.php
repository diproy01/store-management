<?php
require_once __DIR__ . '/init.php';

header('Content-Type: application/json; charset=UTF-8');
$barcode = $_POST['barcode'] ?? '';
if (!is_string($barcode) || $barcode === '') {
    echo json_encode(['no' => 'no']);
    exit;
}

$barcode = $db->connection->real_escape_string($barcode);
$item = $db->queryUniqueObject("SELECT * FROM stock_details WHERE barcode = '$barcode'");
echo json_encode($item ? [
    'sell' => (string) $item->selling_price,
    'stock' => (string) $item->stock_quatity,
    'model' => (string) $item->stock_brand,
    'guid' => (string) $item->stock_name,
] : ['no' => 'no']);
