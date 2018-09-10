<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('institution_id')->unsigned();
            $table->foreign('institution_id')->references('id')->on('institutions')->onDelete('cascade');
            $table->string('mailchimp_list_id')->nullable()->index();
            $table->string('mailchimp_workflow_id')->nullable()->index();
            $table->string('mailchimp_workflow_email_id')->nullable()->index();
            $table->string('name');
            $table->string('domain');
            $table->string('directory');
            $table->string('ftp_server')->nullable();
            $table->string('ftp_username')->nullable();
            $table->string('ftp_path')->nullable();
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
        Schema::dropIfExists('campaigns');
    }
}
