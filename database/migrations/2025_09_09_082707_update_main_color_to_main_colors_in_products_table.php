<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn('main_color'); // احذف القديم
        $table->json('main_colors')->nullable(); // الجديد (array من ألوان)
    });
}

public function down()
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn('main_colors');
        $table->string('main_color')->nullable(); // rollback لو عايز
    });
}

};
