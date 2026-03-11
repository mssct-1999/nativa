<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class BackfillProductBarcodes extends Migration
{
    public function up()
    {
        $products = DB::table('products')
            ->select('id', 'barcode')
            ->orderBy('id')
            ->get();

        foreach ($products as $product) {
            if (! empty($product->barcode)) {
                continue;
            }

            $barcode = $this->generateBarcode();

            DB::table('products')
                ->where('id', $product->id)
                ->update(['barcode' => $barcode]);
        }
    }

    public function down()
    {
        // No rollback to avoid wiping valid barcodes.
    }

    private function generateBarcode(): string
    {
        do {
            $barcode = '2'.str_pad((string) random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
        } while (DB::table('products')->where('barcode', $barcode)->exists());

        return $barcode;
    }
}
