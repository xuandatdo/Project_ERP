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
            if (!Schema::hasTable('vehicles')) {
                Schema::create('vehicles', function (Blueprint $table) {
                    $table->id();
                    $table->string('partner_code');
                    $table->string('name');
                    $table->integer('max_load')->default(0);
                    $table->date('maintenance_date')->nullable();
                    $table->timestamps();
            
                    $table->foreign('partner_code')
                          ->references('code')
                          ->on('partners')
                          ->onDelete('cascade');
                });
            }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
