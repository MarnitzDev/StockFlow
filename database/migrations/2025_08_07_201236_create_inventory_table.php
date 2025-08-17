<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('price', 10, 2);
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('low_stock_threshold')->default(10);
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->string('image_url')->nullable();
            $table->boolean('available_on_pos')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory');
    }
};
