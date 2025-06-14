<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveTypesTable extends Migration
{
    public function up()
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Sick, Casual
            $table->integer('max_days')->default(0); // optional: allowed days per year
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_types');
    }
}
