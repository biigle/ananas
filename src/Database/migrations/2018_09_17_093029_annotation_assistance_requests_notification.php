<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AnnotationAssistanceRequestsNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('annotation_assistance_requests', function (Blueprint $table) {
            // We removed direct notification to any email address because of privacy
            // implications. We don't want to store arbitrary email addresses in our
            // database. Instead, we provide a way to automatically send notifications
            // to other Biigle users. Else, the user has to copy and send the link of
            // the assistance request manually.
            // see: https://github.com/biigle/ananas/issues/2
            $table->dropColumn('email');

            // Optonal receiving Biigle user of this request.
            $table->integer('receiver_id')->unsigned()->nullable();
            $table->foreign('receiver_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('annotation_assistance_requests', function (Blueprint $table) {
            $table->string('email');
            $table->dropColumn('receiver_id');
        });
    }
}
