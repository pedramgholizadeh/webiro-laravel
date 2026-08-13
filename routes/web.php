<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;


Route::view('/', 'pages.homepage');

Route::get('/{page:slug}', PageController::class);
