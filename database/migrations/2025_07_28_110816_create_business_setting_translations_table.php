<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_setting_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_setting_id')->constrained()->onDelete('cascade');
            $table->string('locale', 5); 
            $table->string('shop_name');
            $table->text('address');
            $table->text('access_information')->nullable();
            $table->string('site_name')->nullable();
            $table->text('shop_description')->nullable();
            $table->text('terms_of_use')->nullable();
            $table->text('privacy_policy')->nullable();
            $table->timestamps();
            $table->index(['business_setting_id', 'locale']);
            $table->unique(['business_setting_id', 'locale']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('business_setting_translations');
    }
};