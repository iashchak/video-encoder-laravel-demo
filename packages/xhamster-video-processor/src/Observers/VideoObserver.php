<?php

namespace Iashchak\XhamsterVideoProcessor\Observers;

use Iashchak\XhamsterVideoProcessor\Models\Video;
use Iashchak\XhamsterVideoProcessor\Jobs\ProcessVideo;

class VideoObserver {
    /**
    * Handle the Video "created" event.
    *
    * @param Video $video
    * @return void
    */
    public function created(Video $video)
    {
        // When a video is created, we need to encode it by dispatching a job
        ProcessVideo::dispatch($video);
    }
}