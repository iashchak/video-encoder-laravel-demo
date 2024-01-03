<?php

namespace Iashchak\XhamsterVideoProcessor\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Iashchak\XhamsterVideoProcessor\Models\Video;
use Illuminate\Support\Facades\Log;
use ProtoneMedia\LaravelFFMpeg\FFMpeg\ProgressListenerDecorator;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

enum VideoResolution: string
{
    case RESOLUTION_240P = '240p';
    case RESOLUTION_360P = '360p';
    case RESOLUTION_480P = '480p';
    case RESOLUTION_720P = '720p';
    case RESOLUTION_1080P = '1080p';
    case RESOLUTION_1440P = '1440p';
    case RESOLUTION_2160P = '2160p';
    case RESOLUTION_4K = '4k';
}

enum VideoScale: string
{
    case RESOLUTION_240P = '426:240';
    case RESOLUTION_360P = '640:360';
    case RESOLUTION_720P = '1280:720';
    case RESOLUTION_1080P = '1920:1080';
    case RESOLUTION_1440P = '2560:1440';
    case RESOLUTION_2160P = '3840:2160';
    case RESOLUTION_4K = '4096:2160';
}

class ProcessVideo implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Video $video
    ) {
        Log::info('ProcessVideo constructor');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $format = new \FFMpeg\Format\Video\X264;
        $decoratedFormat = ProgressListenerDecorator::decorate($format);

        Log::info('Video created', ['id' => $this->video->id]);

        $vidInstance = FFMpeg::open(storage_path('' . $this->video->path))->exportForHLS();

        $storagePath = storage_path('' . $this->video->path).'.m3u8';

        // Iterate over each resolution
        foreach (VideoResolution::cases() as $resolution) {
            $vidInstance = $vidInstance->addFormat($decoratedFormat);
        }

        try {
            $vidInstance
                ->onProgress(function () use ($decoratedFormat) {
                    $listeners = $decoratedFormat->getListeners();  // array of listeners

                    $listener = $listeners[0];  // instance of AbstractProgressListener

                    $progress = $listener->getCurrentPass() / $listener->getTotalPass() * 100;

                    Log::info('Video processing', ['id' => $this->video->id, 'progress' => $progress]);
                })
                ->save($storagePath);
        } catch (\Exception $e) {
            Log::error('Error processing video', ['id' => $this->video->id, 'error' => $e->getMessage()]);
        }
    }
}
