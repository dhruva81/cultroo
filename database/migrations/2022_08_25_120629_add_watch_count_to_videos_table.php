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
        Schema::whenTableDoesntHaveColumn('videos', 'watch_count', function (Blueprint $table) {
            $table->unsignedBigInteger('watch_count')->after('sort_order')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::whenTableHasColumn('videos', 'watch_count', function (Blueprint $table) {
            $table->dropColumn('watch_count');
        });
    }
};
