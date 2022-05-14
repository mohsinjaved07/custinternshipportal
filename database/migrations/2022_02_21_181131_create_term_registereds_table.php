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
            $table->unsignedBigInteger('coordinator_id')->nullable();
            $table->foreign('registration_no')->references("registration_no")->on("students");
            $table->foreign('term_name')->references("term_name")->on("terms");
            $table->foreign('coordinator_id')->references("id")->on("coordinators");
            $table->string('organization_ntn_no')->nullable();
            $table->string('organization_name')->nullable();
            $table->string('organization_email')->nullable();
            $table->string('organization_contact')->nullable();
            $table->text('organization_address')->nullable();
            $table->string('organization_website')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->string('supervisor_email')->nullable();
            $table->string('supervisor_designation')->nullable();
            $table->string('supervisor_contact')->nullable();
            $table->string('offer_letter')->nullable();
            $table->string('offer_letter_uploaded_date')->nullable();
            $table->string('internship_report')->nullable();
            $table->string('internship_report_uploaded_date')->nullable();
            $table->string('internship_completion_certificate')->nullable();
            $table->string('internship_completion_certificate_uploaded_date')->nullable();
            $table->string('days_remaining')->nullable();
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
