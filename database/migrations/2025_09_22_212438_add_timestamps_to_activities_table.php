<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('activities', function (Blueprint $table) {
        // فقط أضف updated_at
        $table->timestamp('updated_at')->nullable();
    });
}

public function down()
{
    Schema::table('activities', function (Blueprint $table) {
        $table->dropColumn('updated_at');
    });
}


};
