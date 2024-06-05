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
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->longText('profilePicture')->nullable();
            $table->string('phoneNumber')->nullable();
            $table->text('about')->nullable();
            $table->string('businessAddress')->nullable();
            $table->string('businessDescription')->nullable();
            $table->string('businessEmail')->nullable();
            $table->string('businessIndustry')->nullable();
            $table->string('website1')->nullable();
            $table->string('website2')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_profiles');
    }
};
