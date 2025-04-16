<?php

use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Artisan;

Route::get('/linkstorage', function () {
    //  Artisan::call('storage:link');
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    echo 'ok';
});
