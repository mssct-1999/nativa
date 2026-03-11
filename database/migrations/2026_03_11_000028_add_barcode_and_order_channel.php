<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBarcodeAndOrderChannel extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('barcode')->nullable()->unique()->after('sku');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('channel')->default('online')->after('status');
            $table->dateTime('paid_at')->nullable()->after('shipped_at');
            $table->string('payment_method')->nullable()->after('paid_at');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['barcode']);
            $table->dropColumn('barcode');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['channel', 'paid_at', 'payment_method']);
        });
    }
}
