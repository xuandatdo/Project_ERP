<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_plans', function (Blueprint $table) {
            $table->id();
            $table->string('license_plate');
            $table->decimal('weight', 10, 2);
            $table->dateTime('expected_time');
            $table->enum('status', ['preparing', 'in_transit', 'completed', 'delayed']);
            $table->string('delivery_location');
            $table->string('pickup_location');
            $table->string('driver_name');
            $table->string('driver_phone', 20);
            $table->integer('quantity');
            $table->text('cargo_details')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_plans');
    }
}; 