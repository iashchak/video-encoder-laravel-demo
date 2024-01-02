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

class ProcessVideo implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Video $video
    )
    {
        Log::info('ProcessVideo constructor');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // todo: process the video
        Log::info('Video created', ['id' => $this->video->id]);
    }
}
