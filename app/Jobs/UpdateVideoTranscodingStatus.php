<?php

namespace App\Jobs;

use App\Models\MediaConvertResponse;
use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateVideoTranscodingStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $data;
    private Video $video;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->data;

        $bucket = $data['detail']['userMetadata']['bucket'];
        $appUrl = $data['detail']['userMetadata']['app_url'];

        if($bucket === config('filesystems.disks.s3.bucket') && $appUrl === config('app.url')) {

            $video_id = $data['detail']['userMetadata']['video_id'];
            $job_id = $data['detail']['jobId'];
            $transcodingStatus = $data['detail']['status'];

            MediaConvertResponse::create([
                'video_id' => $video_id,
                'media_convert_job_id' => $job_id,
                'data' => $data['detail']
            ]);

            $video = Video::find($video_id);

            if($video)
            {
                if ($transcodingStatus === 'PROGRESSING') {
                    $video->transcoding_status = 2;
                    Log::info('Video # ' .$video_id . ' transcoding has been started. Status: PROGRESSING');
                }

                if ($transcodingStatus === 'COMPLETE') {
                    $video->transcoding_status = 3;
                    $video->streamable_video_meta = $data['detail']['outputGroupDetails'];
                    Log::info('Video # ' .$video_id . ' transcoding has been completed. Status: COMPLETE');

                    // Update path of streamable video
                    $destinationBucket = config('filesystems.disks.s3.bucket');
                    $video->streamable_video_path = str_replace("s3://{$destinationBucket}/", '', $data['detail']['outputGroupDetails'][0]['playlistFilePaths'][0]);
                }

                if ($transcodingStatus === 'INPUT_INFORMATION') {
                    $video->uploaded_video_meta = $data['detail']['inputDetails'];
                }

                if ($transcodingStatus === 'ERROR') {
                    Log::error('Video transcoding status - ERROR. Video Id: ' . $video_id, $data['detail']);
                    Log::error('Video # ' .$video_id . ' transcoding has error. Status: ERROR');
                }

                $video->save();
            }
            else
            {
                Log::error('Video # ' .$video_id . ' not found. ' .  $data['detail']);
            }
        }
    }
}
