<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUserAddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add columns to the 'users' table
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_id')->unique()->nullable();
            $table->string('department')->nullable();
            $table->string('gender')->nullable();
            $table->string('address')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('image')->nullable();
            $table->date('date_of_joining')->nullable();
            $table->date('date_of_birth')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove columns in case of rollback
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('employee_id');
            $table->dropColumn('department');
            $table->dropColumn('date_of_joining');
            $table->dropColumn('gender');
            $table->dropColumn('address');
            $table->dropColumn('phone_number');
            $table->dropColumn('image');
            $table->dropColumn('date_of_birth');
        });
    }
}
