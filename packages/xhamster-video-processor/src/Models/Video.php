<?php

namespace Iashchak\XhamsterVideoProcessor\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    /**
     * @var string The title of the video
     */
    public $title;
    /**
     * @var string The description of the video
     */
    public $description;
    /**
     * @var string The path to the video file
     */
    public $path;

    /**
     * Video constructor.
     *
     * @param string $title The title of the video
     * @param string $description The description of the video
     * @param string $path The path to the video file
     */
    static public function withData(string $title, string $description, string $path): Video
    {
        $video = new Video();
        $video->title = $title;
        $video->description = $description;
        $video->path = $path;
        return $video;
    }
}
