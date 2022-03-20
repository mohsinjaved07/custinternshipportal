<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternConfirmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intern_confirms', function (Blueprint $table) {
            $table->string("registration_no");
            $table->foreign("registration_no")->references("registration_no")->on("students");
            $table->primary("registration_no");
            $table->longText("link");
            $table->string("expire_date");
            $table->string("status")->nullable();
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
        Schema::dropIfExists('intern_confirms');
    }
}
