<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('address');
            $table->string('phone');
            $table->string('gender');
            $table->string('position');
            $table->string('department');
            $table->string('workplace');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('probation_end')->nullable();
            $table->string('work_hours');
            $table->string('salary_structure');
            $table->string('supervisor');
            $table->string('salary_type');
            $table->decimal('salary_amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
