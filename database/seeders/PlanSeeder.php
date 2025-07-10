<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use Stripe\Stripe;
use Stripe\Product;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // Set your Stripe secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Define the plans, including descriptions and features
        $plans = [
            [
                'name' => 'Starter',
                'amount' => 2900,
                'description' => 'Best for individuals and new projects',
                'features' => [
                    'Individual configuration',
                    'No setup or hidden fees',
                    '1 developer seat',
                    '1 day premium support',
                    '1 day free updates',
                ]
            ],
            [
                'name' => 'Company',
                'amount' => 9900,
                'description' => 'For growing teams with support',
                'features' => [
                    'Custom team management',
                    'No setup or hidden fees',
                    'Up to 10 developer seats',
                    '1 day premium support',
                    '1 day free updates',
                ]
            ],
            [
                'name' => 'Enterprise',
                'amount' => 49900,
                'description' => 'Best for large scale apps and orgs',
                'features' => [
                    'Full-scale configuration',
                    'No setup or hidden fees',
                    '100+ developer seats',
                    '1 day premium support',
                    '1 day free updates',
                ]
            ],
            // ['name' => 'Pro', 'amount' => 19900, 'description' => 'Pro plan for power users'],
        ];

        foreach ($plans as $plan) {

            // Create product with default price data
            $product = Product::create([
                'name' => $plan['name'],
                'description' => $plan['description'],
                'default_price_data' => [
                    'unit_amount' => $plan['amount'], // amount in cents
                    'currency' => 'usd',
                    'recurring' => ['interval' => 'day'],
                ],
                'expand' => ['default_price'],
            ]);

            // Save to local database with description and features
            Plan::create([
                'name' => $plan['name'],
                'stripe_plan_id' => $product->id,
                'stripe_price_id' => $product->default_price->id,
                'desc' => $plan['description'],
                'features' => json_encode($plan['features']),
                'price' => $plan['amount'], // Store the price (amount in cents)
            ]);
        }
    }
}

