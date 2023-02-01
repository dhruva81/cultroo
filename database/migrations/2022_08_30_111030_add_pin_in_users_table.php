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
        Schema::whenTableDoesntHaveColumn('users', 'pin', function (Blueprint $table) {
            $table->unsignedInteger('pin')->after('otp')->nullable();
        });

        Schema::whenTableDoesntHaveColumn('profiles', 'pin', function (Blueprint $table) {
            $table->unsignedInteger('pin')->after('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::whenTableHasColumn('users', 'pin', function (Blueprint $table) {
            $table->dropColumn('pin');
        });

        Schema::whenTableHasColumn('profiles', 'pin', function (Blueprint $table) {
            $table->dropColumn('pin');
        });
    }
};
