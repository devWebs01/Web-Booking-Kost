<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *  'user_id',
     *  'status',
     *  'order_id',
     *  'total'
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_id')->nullable()->unique();
            $table->string('total')->nullable();
            $table->string('snapToken')->nullable();
            $table->enum('status', allowed: ['PENDING', 'CANCEL', 'PROCESS', 'CONFIRM', 'COMPLETE', 'VERIFICATION'])->default('PENDING');
            $table->dateTime('expired_at');
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
