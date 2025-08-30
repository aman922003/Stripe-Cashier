<?php

namespace App\Http\Controllers;

use App\Mail\StripeNotificationMail;
use App\Jobs\SendStripeEmailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestMailController extends Controller
{
    // Show simple form
    public function showForm()
    {
        return view('test-mail-form');
    }

    // Send mail after submitting form
    public function sendMail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $data = [
            'message' => 'This is a test Stripe notification email!',
        ];

        // Mail::to($request->email)->send(new StripeNotificationMail($data));

        // SendStripeEmailJob::dispatch($request->email, $data);
        SendStripeEmailJob::dispatch($request->email, $data)->delay(now()->addSeconds(5));

        return back()->with('success', 'Test email sent successfully!');
    }
}
