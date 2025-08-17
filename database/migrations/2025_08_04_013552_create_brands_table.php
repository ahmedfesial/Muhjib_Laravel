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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->text('logo')->nullable();
            $table->text('short_description_en')->nullable();
            $table->text('short_description_ar')->nullable();
            $table->text('full_description_en')->nullable();
            $table->text('full_description_ar')->nullable();
            $table->text('background_image_url')->nullable();
            $table->string('color_code')->nullable();
            $table->text('catalog_pdf_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
