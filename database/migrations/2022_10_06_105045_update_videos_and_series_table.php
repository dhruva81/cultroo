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
        Schema::whenTableHasColumn('videos', 'short_description', function (Blueprint $table) {
            $table->dropColumn('short_description');
        });

        Schema::whenTableHasColumn('videos', 'max_age', function (Blueprint $table) {
            $table->dropColumn('max_age');
        });

        Schema::whenTableHasColumn('series', 'short_description', function (Blueprint $table) {
            $table->dropColumn('short_description');
        });

        Schema::whenTableHasColumn('series', 'max_age', function (Blueprint $table) {
            $table->dropColumn('max_age');
        });

        Schema::whenTableDoesntHaveColumn('series', 'synopsis', function (Blueprint $table) {
            $table->after('description', function (Blueprint $table) {
                $table->text('synopsis')->nullable();
            });
        });

        Schema::whenTableHasColumn('videos', 'sort_order', function (Blueprint $table) {
            $table->renameColumn('sort_order', 'episode_number');
        });

        Schema::whenTableHasColumn('series', 'season', function (Blueprint $table) {
            $table->renameColumn('season', 'season_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
