<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeCorrectionsTable extends Migration
{
    public function up()
    {
        Schema::create('time_corrections', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->date('date');
            $table->string('type'); // clock_in, clock_out, both
            $table->time('requested_time_in')->nullable();
            $table->time('requested_time_out')->nullable();
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();
            $table->index('employee_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('time_corrections');
    }
}
