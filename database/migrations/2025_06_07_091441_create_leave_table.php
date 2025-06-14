<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveTable extends Migration
{
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->foreignId('leave_type_id')->constrained('leave_types');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days'); // automatically calculated difference
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index('employee_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('leaves');
    }

}
