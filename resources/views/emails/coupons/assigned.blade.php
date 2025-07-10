@component('mail::message')
# Hello {{ $user->name }},

You have been assigned a new coupon: **{{ $coupon->name }}**.

@if($coupon->code)
Your coupon code is: **{{ $coupon->code }}**
@endif

Please log in to your account and use this coupon on your next subscription checkout.

@component('mail::button', ['url' => route('login')])
Log In
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
