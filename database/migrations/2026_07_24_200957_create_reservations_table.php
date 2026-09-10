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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            //TODO: make desk_id nullable.
            $table->foreignId('desk_id')->constrained('desks')->onDelete('cascade');
            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('guests_count');
            //TODO: Change enum to string.
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            //TODO: Add index and migrate again.
//            $table->index(['desk_id', 'date', 'start_time', 'end_time']);
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
