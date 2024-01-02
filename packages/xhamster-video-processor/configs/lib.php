<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    'providers' => [
        ProtoneMedia\LaravelFFMpeg\Support\ServiceProvider::class,
    ],
    'aliases' => [
        'FFMpeg' => ProtoneMedia\LaravelFFMpeg\Support\FFMpeg::class,
    ],
];