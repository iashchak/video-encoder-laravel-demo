<?php

namespace Iashchak\XhamsterVideoProcessor\Jobs;

use Iashchak\XhamsterVideoProcessor\Events\VideoProcessing;
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
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use FFMpeg\Filters\Video\VideoFilters;
use Illuminate\Support\Facades\Bus;
use Exception;
use Illuminate\Bus\Batchable;
class Rectangle
{
    public function __construct(
        public int $width,
        public int $height
    ) {}
}

enum Resolution {
    case RES_240p;
    case RES_360p;
    case RES_480p;
    case RES_720p;
    // case RES_1080p;
    // case RES_1440p;
    // case RES_2160p;
    // case RES_4320p;
    
    function getResolution(): Rectangle {
        switch ($this) {
            case Resolution::RES_240p:
                return new Rectangle(426, 240);
            case Resolution::RES_360p:
                return new Rectangle(640, 360);
            case Resolution::RES_480p:
                return new Rectangle(854, 480);
            case Resolution::RES_720p:
                return new Rectangle(1280, 720);
            // case Resolution::RES_1080p:
            //     return new Rectangle(1920, 1080);
            // case Resolution::RES_1440p:
            //     return new Rectangle(2560, 1440);
            // case Resolution::RES_2160p:
            //     return new Rectangle(3840, 2160);
            // case Resolution::RES_4320p:
            //     return new Rectangle(7680, 4320);
            default:
                throw new Exception("Unknown resolution");
        }
    }
}
class ProcessVideo implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Video $video,
        public int $width,
        public int $height,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $format = new \FFMpeg\Format\Video\X264;
        $decoratedFormat = ProgressListenerDecorator::decorate($format);
        $this->video->update(['status' => 'processing']);
        
        $video = FFMpeg::fromDisk('sourceVideos')
            ->open($this->video->sourceFile)
            ->export()
            ->addFilter(function (VideoFilters $filters) {
                $filters->resize(new \FFMpeg\Coordinate\Dimension($this->width, $this->height));
            })
            ->inFormat($decoratedFormat)
            ->toDisk('processedVideos')
            ->save($this->video->sourceFile . '_' . $this->width . 'x' . $this->height . '.mp4');
    }

    public static function sendToProcessing(Video $video) {
        return Bus::batch(
            array_map(fn($resolution) => new ProcessVideo($video, $resolution->getResolution()->width, $resolution->getResolution()->height), Resolution::cases())
        )
        ->progress(function ($batch) {
            // @todo: emit event to subscribers
            Log::info('Processing video: ' . $batch->progress() . '%');
        })
        ->name("Processing video {$video->id}")
        ->dispatchAfterResponse();
    }
}
