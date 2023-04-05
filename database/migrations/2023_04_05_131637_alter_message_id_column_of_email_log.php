<?php

use Illuminate\Database\Migrations\Migration;

class AddBccColumnEmailLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_log', function ($table) {
            $table->string('messageId', 36)->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_log', function ($table) {
            $table->string('messageId', 32)->nullable()->default(null)->change();
        });
    }
}
