<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->string('organization_ntn_no');
            $table->primary("organization_ntn_no");
            $table->string('organization_name')->nullable();
            $table->string('organization_email')->nullable();
            $table->string('organization_contact')->nullable();
            $table->text('organization_address')->nullable();
            $table->string('organization_website')->nullable();
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
        Schema::dropIfExists('organizations');
    }
}
