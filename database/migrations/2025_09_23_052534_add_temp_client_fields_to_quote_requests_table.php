<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('quote_requests', function (Blueprint $table) {
        $table->string('client_email')->nullable();
        $table->string('client_name')->nullable();
        $table->string('client_phone')->nullable();
        $table->string('client_company')->nullable();
    });
}

public function down()
{
    Schema::table('quote_requests', function (Blueprint $table) {
        $table->dropColumn([
            'client_email',
            'client_name',
            'client_phone',
            'client_company',
        ]);
    });
}

};
