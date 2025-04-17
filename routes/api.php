<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\FileAndFolder\App\Http\Controllers\FileAndFolderController;

Route::post('/upload-file', [FileAndFolderController::class, 'uploadFile']);
