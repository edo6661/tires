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
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['title', 'content']);
        });

        Schema::create('announcement_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->onDelete('cascade');
            $table->string('locale', 2); // 'en', 'ja'
            $table->string('title');
            $table->text('content');
            $table->timestamps();

            $table->index(['announcement_id', 'locale']);
            $table->unique(['announcement_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_translations');
        
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('title');
            $table->text('content');
        });
    }
};