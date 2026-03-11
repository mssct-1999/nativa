<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$products = \App\Models\Product::all();
echo "Checking product barcodes...\n";
echo str_repeat("=", 100) . "\n";

foreach ($products as $product) {
    $barcode = $product->barcode;
    $issues = [];
    
    if (empty($barcode)) {
        $issues[] = "EMPTY";
    } else {
        if (strlen($barcode) != 13) {
            $issues[] = "LENGTH_NOT_13 (current: " . strlen($barcode) . ")";
        }
        if (!ctype_digit($barcode)) {
            $issues[] = "CONTAINS_NON_DIGITS";
        }
    }
    
    $status = empty($issues) ? "✓ OK" : "✗ PROBLEM: " . implode(", ", $issues);
    printf("ID: %3d | Barcode: %-15s | SKU: %-10s | Name: %-30s | %s\n", 
        $product->id,
        $barcode ?: 'NULL',
        $product->sku ?: '-',
        substr($product->name, 0, 30),
        $status
    );
}

echo str_repeat("=", 100) . "\n";
