<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShopFieldsToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shop_id')->nullable()->after('client_id')->constrained('shops')->nullOnDelete();
            $table->foreignId('shop_customer_id')->nullable()->after('shop_id')->constrained('shop_customers')->nullOnDelete();
            $table->decimal('discount', 15, 2)->default(0)->after('total');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropForeign(['shop_customer_id']);
            $table->dropColumn(['shop_id', 'shop_customer_id', 'discount']);
        });
    }
}
