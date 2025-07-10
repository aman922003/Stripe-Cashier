@component('mail::message')
# Hello!

@if(isset($user) && $user->exists)
You are already registered with this email.
You can log in and use your coupon.

@component('mail::button', ['url' => route('login')])
Log In
@endcomponent

@else
You’ve been invited to claim **{{ $coupon->name }}**!

**Coupon Code:** {{ $coupon->code }}

Click below to register and automatically get your coupon.

@component('mail::button', ['url' => $registrationUrl])
Register Now
@endcomponent

@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
