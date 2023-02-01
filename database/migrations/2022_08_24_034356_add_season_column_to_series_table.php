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
        Schema::whenTableDoesntHaveColumn('series', 'parent_id', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->after('status')->nullable();
        });

        Schema::whenTableDoesntHaveColumn('series', 'season', function (Blueprint $table) {
            $table->unsignedInteger('season')->after('parent_id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('series', function (Blueprint $table) {
            $table->dropColumn('parent_id');
            $table->dropColumn('season');
        });
    }
};
