<?php

namespace Lahza\PaymentGateway\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController
{
    public function handlePayment(Request $request)
    {
        $payload = $request->all();

        // Handle webhook events
        switch ($payload['type']) {
            case 'payment.succeeded':
                // Handle successful payment
                break;
            case 'payment.failed':
                // Handle failed payment
                break;
        }

        return response()->json(['status' => 'received']);
    }
}