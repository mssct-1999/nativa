<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesPipelinesTable extends Migration
{
    public function up()
    {
        Schema::create('sales_pipelines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->enum('stage', ['lead','qualified','proposal','negotiation','won','lost'])->default('lead');
            $table->decimal('value', 15, 2)->default(0);
            $table->integer('probability')->default(0);
            $table->date('expected_close')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales_pipelines');
    }
}
