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
        Schema::whenTableHasColumn('profiles', 'is_active_profile', function (Blueprint $table) {
            $table->dropColumn('is_active_profile');
        });

        Schema::whenTableDoesntHaveColumn('users', 'active_profile_id', function (Blueprint $table) {
            $table->unsignedBigInteger('active_profile_id')->after('user_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::whenTableHasColumn('users', 'active_profile_id', function (Blueprint $table) {
            $table->dropColumn('active_profile_id');
        });
    }
};
