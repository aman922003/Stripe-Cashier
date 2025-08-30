<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\TestMailController;

Route::get('/', function () {
    Log::info('update eeeee');
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Pricing Controller route
    Route::get('/pricing', [PricingController::class, 'pricing'])->name('pricing');

    //Checkout Controller route
    Route::get('/checkout/{name}', [CheckoutController::class, 'checkout'])->name('checkout');
    Route::get('/checkout-success', [CheckoutController::class, 'success'])->name('checkout.success');

    // Transaction routes
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

});

    // Webhook route
    // Route::post('/webhook', [WebhookController::class, 'handle']);
       Route::post('/stripe/webhook', [WebhookController::class, '__invoke']);


    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])
            ->name('subscriptions.index');

        Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show'])
            ->name('subscriptions.show');

        Route::post('/subscriptions/{id}/portal', [SubscriptionController::class, 'redirectToStripePortal'])
            ->name('subscriptions.portal');

        Route::post('/subscriptions/{id}/cancel', [SubscriptionController::class, 'cancelSubscription'])
            ->name('subscriptions.cancel');

        Route::post('/subscriptions/{id}/resume', [SubscriptionController::class, 'resumeSubscription'])->name('subscriptions.resume');


    });


        Route::get('/coupons/index', [CouponController::class, 'index'])->name('coupons.index');
        Route::get('/coupons/create', [CouponController::class, 'create'])->name('coupons.create');
        Route::get('/coupons/edit/{id}', [CouponController::class, 'edit'])->name('coupons.edit');
        Route::put('/coupons/{id}', [CouponController::class, 'update'])->name('coupons.update');
        Route::post('/coupons', [CouponController::class, 'store'])->name('coupons.store');
        Route::delete('/coupons/{id}', [CouponController::class, 'destroy'])->name('coupons.destroy');
        Route::get('/coupons/{id}/assign', [CouponController::class, 'assignForm'])->name('coupons.assign.form');
        Route::post('/coupons/{id}/assign', [CouponController::class, 'assignToUsers'])->name('coupons.assign');
        Route::post('/coupons/{id}/send-to-friend', [CouponController::class, 'sendToFriend'])->name('coupons.send.to.friend');
        Route::get('/coupons/assigned-users', [CouponController::class, 'viewAllAssignedUsers'])->name('coupons.assigned.users.all');
        Route::post('/coupons/{couponId}/unassign/{userId}', [CouponController::class, 'unassignUser'])
            ->name('coupons.unassign');


        // Transaction Refund payment
        // Route::post('transactions/{invoice}/refund', [TransactionController::class, 'refund'])->name('transactions.refund');
        // Route::post('transactions/{invoice}/cancelRefund', [TransactionController::class, 'cancelRefund'])->name('transactions.cancelRefund');
        Route::middleware(['auth'])->group(function () {

        Route::post('transactions/{invoice}/refund', [TransactionController::class, 'refund'])->name('transactions.refund');
        Route::post('transactions/{invoice}/cancelRefund', [TransactionController::class, 'cancelRefund'])->name('transactions.cancelRefund');
});

        Route::get('/test-mail', [TestMailController::class, 'showForm'])->name('test.mail.form');
        Route::post('/test-mail', [TestMailController::class, 'sendMail'])->name('test.mail.send');
        dgdfgfg





require __DIR__.'/auth.php';
