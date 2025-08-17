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
            $table->string('name_en');
            $table->string('name_ar');
            $table->text('features')->nullable();
            $table->string('main_color')->nullable();
            $table->foreignId('sub_category_id')->constrained()->onDelete('cascade');
            $table->text('main_image')->nullable();
            $table->text('pdf_hs')->nullable();
            $table->text('pdf_msds')->nullable();
            $table->text('pdf_technical')->nullable();
            $table->string('hs_code')->nullable();
            $table->string('sku')->nullable();
            $table->string('pack_size')->nullable();
            $table->string('dimensions')->nullable();
            $table->string('capacity')->nullable();
            $table->text('specification')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
