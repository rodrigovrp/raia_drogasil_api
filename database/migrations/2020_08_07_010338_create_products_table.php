<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id')->nullable();
            $table->integer('brand_id')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('code')->nullable();
            $table->bigInteger('ean')->nullable();
            $table->decimal('weight', 13, 2)->nullable();
            $table->enum('weight_type', ['kg', 'g'])->nullable();
            $table->decimal('quantity', 13, 2)->nullable();
            $table->enum('quantity_type', ['l', 'ml'])->nullable();
            $table->tinyInteger('status')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
