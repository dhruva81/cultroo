<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('dropdown_animation', function () {
            return '
                 x-description="Dropdown panel, show/hide based on dropdown state."
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 ';
        });

        File::macro('streamUpload', function($path, $fileName, $file, $overWrite = false) {
            // Set up S3 connection.
            $resource = fopen($file->getRealPath(), 'r+');
            $config = Config::get('filesystems.disks.s3');
            $client = new S3Client([
                'credentials' => [
                    'key'    => $config['key'],
                    'secret' => $config['secret'],
                ],
                'region' => $config['region'],
                'version' => 'latest',
            ]);

            $adapter = new AwsS3V3Adapter($client, $config['AWS_S3_BUCKET'], $path);
            $filesystem = new Filesystem($adapter);

//            return $overWrite
//                ? $filesystem->putStream($fileName, $resource)
//                : $filesystem->writeStream($fileName, $resource);

            return $filesystem->writeStream($fileName, $resource);
        });

        Relation::enforceMorphMap([
            'user' => 'App\Models\User',
            'profile' => 'App\Models\Profile',
            'series' => 'App\Models\Series',
            'video' => 'App\Models\Video',
            'character' => 'App\Models\Character',
            'genre' => 'App\Models\Genre',
            'kollection' => 'App\Models\Kollection',
            'plan' => 'App\Models\Plan',
            'subscription' => 'App\Models\Subscription',
            'language' => 'App\Models\Language',
            'section' => 'App\Models\Section',
        ]);

        Scramble::extendOpenApi(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer', 'JWT')
            );
        });

        Gate::define('viewApiDocs', function (User $user) {
            return in_array($user->email, ['super@admin.com']);
        });
    }
}
