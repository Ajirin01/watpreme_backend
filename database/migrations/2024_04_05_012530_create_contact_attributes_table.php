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
        Schema::create('contact_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('custom_attribute_id');
            $table->foreign('custom_attribute_id')->references('id')->on('custom_attributes')->onDelete('cascade');
            $table->unsignedBigInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('contact')->onDelete('cascade');
            $table->string('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_attributes');
    }
};
