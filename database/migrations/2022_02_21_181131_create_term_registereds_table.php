<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTermRegisteredsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('term_registereds', function (Blueprint $table) {
            $table->id();
            $table->string('registration_no');
            $table->string('term_name');
            $table->unsignedBigInteger('coordinator_id');
            $table->foreign('registration_no')->references("registration_no")->on("students");
            $table->foreign('term_name')->references("term_name")->on("terms");
            $table->foreign('coordinator_id')->references("id")->on("coordinators");

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
        Schema::dropIfExists('term_registereds');
    }
}
