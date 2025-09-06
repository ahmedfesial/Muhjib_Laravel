<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('template_cover_images', function (Blueprint $table) {
        $table->string('background_position')->nullable(); // 'client', 'products'
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_images', function (Blueprint $table) {
            //
        });
    }
};
