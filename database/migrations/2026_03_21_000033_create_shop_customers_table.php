<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('shop_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('fidelity_score')->default(0);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('total_spent', 15, 2)->default(0);
            $table->unsignedInteger('orders_count')->default(0);
            $table->dateTime('last_order_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shop_customers');
    }
}
