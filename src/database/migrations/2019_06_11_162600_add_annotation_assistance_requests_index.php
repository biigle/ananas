<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnnotationAssistanceRequestsIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('annotation_assistance_requests', function (Blueprint $table) {
            $table->index('annotation_id');
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
            $table->dropIndex(['annotation_id']);
        });
    }
}
