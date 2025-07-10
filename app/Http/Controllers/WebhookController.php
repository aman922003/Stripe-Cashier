<?php

// app/Http/Controllers/WebhookController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Subscription;
use Stripe\PromotionCode;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;

class WebhookController extends CashierWebhookController
{
    public function __invoke(Request $request)
    {
        return $this->handleWebhook($request);
    }

    protected function handleCustomerSubscriptionUpdated(array $payload)
    {
        $subscription = $payload['data']['object'];

        // Extract current_period_end from subscription items if it's missing from the main object
        if (!isset($subscription['current_period_end']) && isset($subscription['items']['data'][0]['current_period_end'])) {
            $subscription['current_period_end'] = $subscription['items']['data'][0]['current_period_end'];
            $subscription['current_period_start'] = $subscription['items']['data'][0]['current_period_start'];

            // Update the payload to include these fields at the subscription level
            $payload['data']['object']['current_period_end'] = $subscription['current_period_end'];
            $payload['data']['object']['current_period_start'] = $subscription['current_period_start'];

            Log::info('Added current_period_end from subscription item', [
                'current_period_end' => $subscription['current_period_end'],
                'current_period_start' => $subscription['current_period_start']
            ]);
        }

        $result = parent::handleCustomerSubscriptionUpdated($payload);
        return $result;
    }

    //  protected function handleCheckoutSessionCompleted(array $payload)
    // {
    //     $session = $payload['data']['object'];

    //     if (isset($session['subscription']) && isset($session['total_details'])) {
    //         $subscriptionId = $session['subscription'];

    //         // Get the promotion code from the session discounts if any
    //         if (isset($session['discounts']) && count($session['discounts']) > 0) {
    //             $promotionCodeId = $session['discounts'][0]['promotion_code'];

    //             // Attach the promo code directly to the subscription for recurring invoices
    //             $subscription = Subscription::retrieve($subscriptionId);
    //             $subscription->discounts = [
    //                 ['promotion_code' => $promotionCodeId],
    //             ];
    //             $subscription->save();
    //         }
    //     }
    // }


}
