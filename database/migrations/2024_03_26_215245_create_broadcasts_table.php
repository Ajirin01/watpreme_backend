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
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Changed from 'title' to 'name'
            $table->foreignId('template_id')->constrained()->onDelete('cascade');
            $table->string('channel'); // Added 'channel' field
            $table->string('status')->default('pending');
            $table->json('recipients'); // Changed from 'contacts' to 'recipients'
            $table->timestamp('sent_date')->nullable(); // Added 'sent_date' field
            $table->timestamp('posting_time')->nullable(); 
            $table->boolean('is_scheduled')->default(false); // Added 'is_scheduled' field
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broadcasts');
    }
};
