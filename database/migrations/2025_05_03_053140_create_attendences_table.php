<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('attendances')) {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->date('date')->index();
            $table->enum('status', ['present','absent','leave'])->default('present');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->timestamps();

            $table->unique(['employee_id','date']);
        });
    }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendences');
    }
}
