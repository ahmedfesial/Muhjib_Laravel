<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('clients', function (Blueprint $table) {
        $table->string('logo')->nullable()->after('company');

        // تعديل status إلى enum
        $table->enum('status', ['pending', 'approved', 'rejected'])
              ->default('approved')
              ->after('logo');
    });
}

public function down()
{
    Schema::table('clients', function (Blueprint $table) {
        $table->dropColumn(['logo', 'status']);
    });
}
};
