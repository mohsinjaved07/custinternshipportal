<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->string('term_name');
            $table->primary("term_name");
            $table->string("sem_name");
            $table->date('term_started_date');
            $table->string('apply_for_internship')->nullable();
            $table->string('upload_offer_letter_date')->nullable();
            $table->string('upload_document_date')->nullable();
            $table->string('internship_plan')->nullable();
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
        Schema::dropIfExists('terms');
    }
}
