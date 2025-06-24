<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login.html', function () {
    return view('login');
});

Route::get('/sign_up.html', function () {
    return view('sign_up');
});

Route::get('/userHomePage.html', function () {
    return view('userHomePage');
});


