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
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
        });
        Schema::create('menu_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->string('locale', 2); 
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['menu_id', 'locale']);
            $table->unique(['menu_id', 'locale']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_translations');
        Schema::table('menus', function (Blueprint $table) {
            $table->string('name');
            $table->text('description')->nullable();
        });
    }
};