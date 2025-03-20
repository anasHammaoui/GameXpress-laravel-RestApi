<?php

use App\Http\Controllers\Api\V3\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});