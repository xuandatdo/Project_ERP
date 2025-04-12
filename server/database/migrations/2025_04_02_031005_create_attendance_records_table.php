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
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id');
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent_with_permission', 'absent_without_permission', 'late']);
            $table->unsignedInteger('late_count')->default(0);
            $table->string('note')->nullable();
            $table->timestamps();
            
            // Add foreign key constraint if needed
            $table->foreign('employee_id')->references('id')->on('employees')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};