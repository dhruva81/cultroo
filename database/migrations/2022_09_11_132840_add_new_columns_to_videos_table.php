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
        Schema::whenTableHasColumn('videos', 'slug', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->after('run_time', function (Blueprint $table) {
                $table->json('uploaded_video_meta')->nullable();
                $table->json('streamable_video_meta')->nullable();
                $table->tinyInteger('transcoding_status')->nullable();
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
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn([
                'uploaded_video_meta',
                'streamable_video_meta',
                'transcoding_status',
            ]);
        });
    }
};
