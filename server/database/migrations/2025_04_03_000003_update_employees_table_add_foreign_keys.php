<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmployeesTableAddForeignKeys extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Đổi tên cột cũ để giữ lại dữ liệu
            $table->renameColumn('department', 'department_name');
            $table->renameColumn('position', 'position_name');
            
            // Thêm cột khóa ngoại mới
            $table->unsignedBigInteger('department_id')->nullable()->after('gender');
            $table->unsignedBigInteger('position_id')->nullable()->after('department_id');
            
            // Tạo ràng buộc khóa ngoại
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Xóa khóa ngoại
            $table->dropForeign(['department_id']);
            $table->dropForeign(['position_id']);
            
            // Xóa cột khóa ngoại
            $table->dropColumn('department_id');
            $table->dropColumn('position_id');
            
            // Đổi tên cột về tên cũ
            $table->renameColumn('department_name', 'department');
            $table->renameColumn('position_name', 'position');
        });
    }
}