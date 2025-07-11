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
        Schema::create('blocked_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->nullable()->constrained()->onDelete('cascade');
            $table->datetime('start_datetime');
            $table->datetime('end_datetime');
            $table->string('reason');
            $table->boolean('all_menus')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_periods');
    }
};
