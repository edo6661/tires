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
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->json('business_hours'); // ini json bang
            $table->string('website_url')->nullable();
            $table->string('top_image_path')->nullable();
            $table->boolean('site_public')->default(true);
            $table->string('reply_email')->nullable();
            $table->string('google_analytics_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_settings');
    }
};
