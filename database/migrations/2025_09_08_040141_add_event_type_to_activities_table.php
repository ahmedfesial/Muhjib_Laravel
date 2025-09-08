<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('activities', function (Blueprint $table) {
        // تأكد من وجود الأعمدة دي
        if (!Schema::hasColumn('activities', 'event_type')) {
            $table->string('event_type')->after('user_id');
        }
        if (!Schema::hasColumn('activities', 'description')) {
            $table->text('description')->after('event_type');
        }
    });
}

public function down()
{
    Schema::table('activities', function (Blueprint $table) {
        $table->dropColumn('event_type');
        $table->dropColumn('description');
    });
}

};
