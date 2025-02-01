<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 'gross_amount',
     *  'payment_time',
     *  'payment_type',
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings');
            $table->string('gross_amount')->nullable();
            $table->string('payment_time')->nullable();
            $table->string('payment_type')->nullable();
            $table->longText('payment_detail')->nullable();
            $table->longText('status_message')->nullable();
            $table->enum('status', ['PAID', 'UNPAID', 'FAILED', 'PROCESS'])->default('UNPAID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
