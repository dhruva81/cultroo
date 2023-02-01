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
        Schema::whenTableDoesntHaveColumn('sections', 'layout', function (Blueprint $table) {
            $table->after('model', function (Blueprint $table) {
                $table->unsignedTinyInteger('layout')->nullable();
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
        Schema::whenTableHasColumn('sections', 'layout', function (Blueprint $table) {
            $table->dropColumn('layout');
        });
    }
};
