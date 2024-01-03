<?php

namespace Iashchak\XhamsterVideoProcessor\Providers;

use Illuminate\Support\ServiceProvider;
use Iashchak\XhamsterVideoProcessor\Models\Video;
use Iashchak\XhamsterVideoProcessor\Observers\VideoObserver;

class XhamsterVideoProcessorServiceProvider extends ServiceProvider
{
    public function boot()
    {

        $this->publishes([
            __DIR__ . '/../../configs/lib.php' => config_path('lib.php'),
            __DIR__ . '/../../configs/laravel-ffmpeg.php' => config_path('laravel-ffmpeg.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        Video::observe(VideoObserver::class);
    }

    public function register()
    {
    }
}
