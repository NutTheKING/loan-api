<?php

use Illuminate\Support\Facades\File;

Route::get('/admin/{any?}', function () {
    return File::get(public_path('admin/index.html'));
})->where('any', '.*');
