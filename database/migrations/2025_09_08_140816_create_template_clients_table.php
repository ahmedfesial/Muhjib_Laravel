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
        Schema::create('template_clients', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('template_id')->nullable();
    $table->unsignedBigInteger('client_id')->nullable();
    $table->string('client_name')->nullable();
    $table->string('email')->nullable();
    $table->string('phone')->nullable();
    $table->string('address')->nullable();
    $table->timestamps();

    $table->foreign('template_id')->references('id')->on('templates')->onDelete('cascade');
    $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_clients');
    }
};
