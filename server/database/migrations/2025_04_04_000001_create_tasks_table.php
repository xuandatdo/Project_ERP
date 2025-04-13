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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('priority', ['Cao', 'Trung bình', 'Thấp']);
            $table->integer('progress')->default(0);
            $table->enum('status', ['Chưa bắt đầu', 'Đang thực hiện', 'Hoàn thành', 'Tạm dừng']);
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Tạo bảng để lưu trữ file đính kèm
        Schema::create('task_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_attachments');
        Schema::dropIfExists('tasks');
    }
};