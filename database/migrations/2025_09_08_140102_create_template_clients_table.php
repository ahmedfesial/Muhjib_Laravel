<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();          // اسم العميل
            $table->string('email')->nullable();         // إيميل العميل
            $table->string('phone')->nullable();         // رقم الهاتف
            $table->unsignedBigInteger('client_id')->nullable(); // علاقة بالجدول الرئيسي لو محتاج

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_clients');
    }
};
