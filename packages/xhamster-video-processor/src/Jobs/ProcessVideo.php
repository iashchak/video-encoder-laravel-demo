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
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use FFMpeg\Filters\Video\VideoFilters;



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
        // list of available resolutions from 240p to 4k with array of dimensions [width, height]
        $resolutions = [
            '240p' => [426, 240],
            '360p' => [640, 360],
            '480p' => [854, 480],
            '720p' => [1280, 720],
            '1080p' => [1920, 1080],
            '1440p' => [2560, 1440],
            '2160p' => [3840, 2160],
        ];
        $format = new \FFMpeg\Format\Video\X264;
        $decoratedFormat = ProgressListenerDecorator::decorate($format);

        Log::info('Video created', ['id' => $this->video->id]);

        $video = FFMpeg::fromDisk('sourceVideos')
        ->open($this->video->sourceFile)
        ->export()
        ->toDisk('processedVideos')
        ->inFormat(new \FFMpeg\Format\Video\X264);

            // ->addFilter(function (VideoFilters $filters) {
            //     $filters->resize(new \FFMpeg\Coordinate\Dimension(640, 480));
            // })
            // ->save($this->video->sourceFile . '.mp4');

        foreach ($resolutions as $resolution) { 
            $video->addFilter(function (VideoFilters $filters) use ($resolution) {
                $filters->resize(new \FFMpeg\Coordinate\Dimension($resolution[0], $resolution[1]));
            })
            ->save($this->video->sourceFile . '_' . $resolution[0] . 'x' . $resolution[1] . '.mp4');
        }
    }
}
