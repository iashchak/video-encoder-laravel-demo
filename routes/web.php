<?php

use Illuminate\Support\Facades\Route;

use Iashchak\XhamsterVideoProcessor\Models\Video;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    Video::create(['title' => 'MYTITLE', 'description' => 'DESC', 'sourceFile' => 'cat.webm'])->save();
    return view('welcome');
});
