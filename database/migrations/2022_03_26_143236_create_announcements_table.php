<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string("registration_no");
            $table->foreign("registration_no")->references("registration_no")->on("students");
            $table->string("purpose");
            $table->text("description");
            $table->string("start_date");
            $table->string("end_date");
            $table->unsignedBigInteger("coordinator_id")->nullable();
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
        Schema::dropIfExists('announcements');
    }
}
