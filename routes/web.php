<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::welcome')
    ->name('home')
    ->withHead(title: 'Welcome');
