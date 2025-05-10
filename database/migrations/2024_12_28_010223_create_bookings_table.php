<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('booking_type');
            $table->string('check_in_date');
            $table->string('check_out_date');
            $table->string('price');
            $table->enum('status', allowed: ['PENDING', 'CANCEL', 'PROCESS', 'CONFIRM', 'COMPLETE', 'VERIFICATION'])->default('PENDING');

            $table->string('order_id')->nullable()->unique();
            $table->string('snapToken')->nullable();
            $table->dateTime('expired_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
