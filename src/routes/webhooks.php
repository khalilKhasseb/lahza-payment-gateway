<?php

use Illuminate\Support\Facades\Route;
use Lahza\PaymentGateway\Http\Middleware\VerifyLahzaWebhook;

Route::group([
    'prefix' => 'webhook/lahza',
    'middleware' => VerifyLahzaWebhook::class
], function () {
    Route::post('payment', 'Lahza\PaymentGateway\Http\Controllers\WebhookController@handlePayment');
});