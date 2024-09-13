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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('contacts')->onDelete('cascade'); // Foreign key to contacts
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable(); // Add end_time column
            $table->string('status');
            $table->uuid('uuid')->unique(); // Ensure uuid is unique
            $table->timestamps();
            $table->unsignedBigInteger('business_id');

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');

            // Indexes for performance
            $table->index('status');
            $table->index('start_time');
            $table->index('end_time');

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
