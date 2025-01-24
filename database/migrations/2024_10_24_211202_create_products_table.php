<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->longText('product_description');
            $table->integer('product_price');
            $table->string('product_video')->nullable();
            $table->integer('product_quantity');
            $table->float('weight')->nullable();
            $table->float('size')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('views_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.2
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
