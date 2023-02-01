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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('pg_name');
            $table->text('pg_description')->nullable();
            $table->string('pg_plan_id')->unique();
            $table->integer('pg_billing_amount')->nullable();
            $table->string('pg_billing_period')->nullable();    // monthly, yearly
            $table->integer('payment_gateway')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_free')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->text('meta')->nullable();
            $table->uuid('uuid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
};
