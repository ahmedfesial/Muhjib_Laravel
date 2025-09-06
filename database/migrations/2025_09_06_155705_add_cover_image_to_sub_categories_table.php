<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('sub_categories', function (Blueprint $table) {
        $table->string('cover_image')->nullable(); // صورة الغلاف
        $table->string('background_image')->nullable(); // خلفية المنتجات (لو عايزها كمان)
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_categories', function (Blueprint $table) {
            //
        });
    }
};
