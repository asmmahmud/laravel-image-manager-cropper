<?php

$urlPrefix = config('imagemanager.admin_url_prefix');

Route::group(['prefix' => $urlPrefix . "/imagemanager"], function () {
    $namespacePrefixForMedia = '\\Tasmnaguib\\Imagemanager\\Http\\Controllers\\ImageManagerController';
    Route::get('/', $namespacePrefixForMedia . '@index')
        ->name('imagemanager.index');
    Route::post('files', $namespacePrefixForMedia . '@files')
        ->name('imagemanager.files');
    Route::post('new_folder', $namespacePrefixForMedia . '@new_folder')
        ->name('imagemanager.new_folder');
    Route::post('upload_cropped', $namespacePrefixForMedia . '@uploadCropped')
        ->name('imagemanager.upload_cropped_files');
    Route::post('delete_multi_files', $namespacePrefixForMedia . '@delete_multi_files')
        ->name('imagemanager.delete_multi_files');
    Route::post('delete_folder', $namespacePrefixForMedia . '@delete_file_folder')
        ->name('imagemanager.delete_file_folder');
    Route::get('directories', $namespacePrefixForMedia . '@get_all_dirs')
        ->name('imagemanager.get_all_dirs');
    Route::post('move_file', $namespacePrefixForMedia . '@move_file')
        ->name('imagemanager.move_file');
    Route::post('rename_file', $namespacePrefixForMedia . '@rename_file')
        ->name('imagemanager.rename_file');
    Route::post('upload', $namespacePrefixForMedia . '@upload')
        ->name('imagemanager.upload');
});
