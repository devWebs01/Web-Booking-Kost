<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
            $table->enum('status', ['PAID', 'UNPAID', 'FAILED', 'PROCESS', 'VERIFICATION'])->default('UNPAID');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
