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
        Schema::create('operators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // $table->string('emailPhone');
            // $table->string('role');
            $table->string('online_status')->nullable();
            $table->string('lastLoginIP')->nullable();
            $table->timestamp('lastLoginDate')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending', 'approved', 'declined'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operators');
    }
};
