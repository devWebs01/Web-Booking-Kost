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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete(); // Nullable untuk pelanggan yang tidak terdaftar
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->enum('session', [
                'daily',
                'weekly',
                'monthly',
            ]);
            $table->string('booking_source')->nullable();
            $table->string('ota_source')->nullable();
            $table->string('customer_name')->nullable(); // Nama pelanggan untuk offline
            $table->string('customer_contact')->nullable(); // Kontak pelanggan untuk offline
            $table->enum('status', ['pending', 'confirmed', 'canceled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
