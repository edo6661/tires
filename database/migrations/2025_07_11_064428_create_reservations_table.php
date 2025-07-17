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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('full_name')->nullable()->after('user_id');
            $table->string('full_name_kana')->nullable()->after('full_name');
            $table->string('email')->nullable()->after('full_name_kana');
            $table->string('phone_number')->nullable()->after('email');
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->datetime('reservation_datetime');
            $table->integer('number_of_people')->default(1);
            $table->decimal('amount', 10, places: 2);
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
