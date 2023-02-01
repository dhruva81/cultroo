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
        Schema::whenTableDoesntHaveColumn('searches', 'is_visible', function (Blueprint $table) {
            $table->after('profile_id', function (Blueprint $table) {
                $table->boolean('is_visible')->default(true);
            });
        });

        Schema::whenTableDoesntHaveColumn('watch_histories', 'is_visible', function (Blueprint $table) {
            $table->after('profile_id', function (Blueprint $table) {
                $table->boolean('is_visible')->default(true);
            });
        });

        Schema::whenTableDoesntHaveColumn('profiles', 'tracking_search_history', function (Blueprint $table) {
            $table->after('avatar_id', function (Blueprint $table) {
                $table->boolean('tracking_search_history')->default(true);
            });
        });

        Schema::whenTableDoesntHaveColumn('profiles', 'tracking_watch_history', function (Blueprint $table) {
            $table->after('avatar_id', function (Blueprint $table) {
                $table->boolean('tracking_watch_history')->default(true);
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
        Schema::whenTableHasColumn('profiles', 'tracking_watch_history', function (Blueprint $table) {
            $table->dropColumn('tracking_watch_history');
        });

        Schema::whenTableHasColumn('profiles', 'tracking_search_history', function (Blueprint $table) {
            $table->dropColumn('tracking_search_history');
        });

        Schema::whenTableHasColumn('watch_histories', 'is_visible', function (Blueprint $table) {
            $table->dropColumn('is_visible');
        });

        Schema::whenTableHasColumn('searches', 'is_visible', function (Blueprint $table) {
            $table->dropColumn('is_visible');
        });
    }
};
