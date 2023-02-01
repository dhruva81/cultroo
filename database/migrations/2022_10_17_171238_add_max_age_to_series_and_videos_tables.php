<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::whenTableDoesntHaveColumn('videos', 'max_age', function (Blueprint $table) {
            $table->after('min_age', function($table){
                $table->unsignedTinyInteger('max_age')->nullable();
            });
        });

        Schema::whenTableDoesntHaveColumn('series', 'max_age', function (Blueprint $table) {
            $table->after('min_age', function($table){
                $table->unsignedTinyInteger('max_age')->nullable();
            });
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('series_and_videos_tables', function (Blueprint $table) {
            //
        });
    }
};
