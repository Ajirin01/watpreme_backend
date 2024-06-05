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
        Schema::create('template_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->string('status')->default('pending');
            $table->json('language');
            $table->boolean('uploaded')->default(false);
            $table->json('components');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_messages');
    }
};
