<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnotationAssistanceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         | With an annotation assistance request a BIIGLE user can consult some external
         | person (who does not have to have a BIIGLE account) for a specific annotation.
         | The requesting user can write a short question text, select optional label
         | suggestions and then submits the request to the "assistant". The assistant
         | can explore the image with the annotation, write a short response text, select
         | one of the label suggestions (if there are any) and close the assistance
         | request.
         */
        Schema::create('annotation_assistance_requests', function (Blueprint $table) {
            $table->increments('id');

            // The token for the single-use URL that the assistant receives.
            $table->string('token', 64)->unique();

            // Email address of the assistant.
            $table->string('email');

            $table->text('request_text');
            $table->text('response_text')->nullable();

            // ids, names and colors of the suggested labels. Store them explicitly as
            // JSON instead of a reference to the actual DB entries so the assistance
            // request remains valid and readable even if the labels were deleted or
            // renamed in the meantime.
            $table->json('request_labels')->nullable();

            // ID of the suggested label that was finally chosen by the assistant.
            // The ID is not directly related to the ID of the actual DB entry of the
            // label for the same reasons as stated at the request_labels column.
            $table->integer('response_label_id')->unsigned()->nullable();

            // If the annotation is deleted, delete the assistance request as well.
            $table->integer('annotation_id')->unsigned();
            $table->foreign('annotation_id')
                ->references('id')
                ->on('annotations')
                ->onDelete('cascade');

            // Creator of the assistance request.
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->timestamps();
            // Signifies when/whether the assistance request got a response from the
            // assistant.
            $table->timestamp('closed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('annotation_assistance_requests');
    }
}
