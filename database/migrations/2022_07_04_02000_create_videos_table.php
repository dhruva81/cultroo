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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();                      // identifier
            $table->string('title');    // title of the video
            $table->string('slug');     // slug
            $table->text('short_description')->nullable();  // short_description
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('status')->nullable();
            $table->unsignedTinyInteger('language_id')->nullable();
            $table->unsignedBigInteger('series_id')->nullable();
            $table->unsignedTinyInteger('min_age')->default(1);
            $table->unsignedTinyInteger('max_age')->nullable();
            $table->unsignedTinyInteger('motion_type')->nullable();
            $table->unsignedInteger('run_time')->nullable();    // This should be in seconds
            $table->dateTime('released_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->uuid('uuid');
            $table->softDeletes();
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
        Schema::dropIfExists('videos');
    }
};
