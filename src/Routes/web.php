<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

// Frontend
require __DIR__ . '/web_frontend.php';

// Backend
require __DIR__ . '/web_backend.php';

// Mobile
require __DIR__ . '/web_mobile.php';

// Seller
require __DIR__ . '/web_seller.php';

// 系统助手
require __DIR__ . '/web_helper.php';

// 公共端
require __DIR__ . '/web_common.php';

// Test
require __DIR__ . '/web_test.php';
