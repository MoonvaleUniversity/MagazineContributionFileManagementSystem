<?php

use Illuminate\Support\Facades\Route;
use Modules\FileAndFolder\App\Http\Controllers\FileAndFolderController;

Route::get('/folders/{folder?}', [FileAndFolderController::class, 'showFolderContents'])->where('folder', '.*')->name('folder.view');

Route::get('/download/{type}/{encodedPath}', [FileAndFolderController::class, 'download'])->where('encodedPath', '.*')->name('file.download');

Route::get('/delete-file/{type}/{file}', [FileAndFolderController::class, 'destroy'])->where('file', '.*')->name('file.delete');

Route::get('/file/{filename}', [FileAndFolderController::class, 'showFile'])->where('filename', '.*')->name('file.view');

Route::get('/restore-file/{type}/{path}', [FileAndFolderController::class, 'restoreFile'])->where('path', '.*')->name('file.restore');

Route::get('/permenent-delete/{type}/{file}', [FileAndFolderController::class, 'permenentDelete'])->where('file', '.*');
