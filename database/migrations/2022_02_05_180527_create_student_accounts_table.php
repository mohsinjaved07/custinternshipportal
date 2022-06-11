<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_accounts', function (Blueprint $table) {
            $table->string("registration_no");
            $table->string("login_id");
            $table->unique("login_id");
            $table->string("password");
            $table->string("login_date");
            $table->string("one_time_auth")->nullable();
            $table->foreign('registration_no')->references("registration_no")->on("students");
            $table->primary("registration_no");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_accounts');
    }
}
