<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-white leading-tight">
                {{ __('Subscription Details') }}
            </h2>
            <a href="{{ route('subscriptions.index') }}"
               class="inline-block px-5 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-medium rounded-md shadow-md transition transform hover:scale-105">
                Back to Subscriptions
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 shadow sm:rounded-lg overflow-hidden">
                <div class="p-8 text-gray-900 dark:text-gray-100">

                    @if (session('error'))
                        <div class="mb-4 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 px-4 py-3 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 px-4 py-3 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    <h1 class="text-3xl font-bold mb-8 text-gray-800 dark:text-white">
                        Subscription Details
                    </h1>

                    <div class="space-y-4 text-lg text-gray-700 dark:text-gray-300">
                        <p><span class="font-semibold">Stripe ID:</span> {{ $sub->stripe_id }}</p>
                        <p><span class="font-semibold">Status:</span> {{ $sub->stripe_status }}</p>
                        <p><span class="font-semibold">Billing Interval:</span>
                            {{ ucfirst(optional($sub->asStripeSubscription()->items->data[0]->price->recurring)->interval ?? 'N/A') }}
                        </p>
                        <p><span class="font-semibold">Current Period:</span>
                            {{ optional($sub->asStripeSubscription())->current_period_start ? date('M d, Y', $sub->asStripeSubscription()->current_period_start) : 'N/A' }}
                            –
                            {{ optional($sub->asStripeSubscription())->current_period_end ? date('M d, Y', $sub->asStripeSubscription()->current_period_end) : 'N/A' }}
                        </p>
                    </div>

                    <div class="mt-10 flex flex-col sm:flex-row flex-wrap gap-4">
                        <form action="{{ route('subscriptions.portal', $sub->id) }}" method="POST">
                            @csrf
                            <button
                                class="w-full sm:w-auto px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition transform hover:scale-105">
                                Open Billing Portal
                            </button>
                        </form>

                      @if (! $sub->ended() && ! $sub->onGracePeriod() && ! $stripeSub->cancel_at_period_end)
                            <form action="{{ route('subscriptions.cancel', $sub->id) }}" method="POST"
                                  onsubmit="return confirm('Cancel at period end?')">
                                @csrf
                                <button
                                    class="w-full sm:w-auto px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow transition transform hover:scale-105">
                                    Cancel Subscription
                                </button>
                            </form>
                        @endif

                        @if ($sub->onGracePeriod())
                            <form action="{{ route('subscriptions.resume', $sub->id) }}" method="POST"
                                  onsubmit="return confirm('Are you sure you want to renew this subscription?')">
                                @csrf
                                <button
                                    class="w-full sm:w-auto px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow transition transform hover:scale-105">
                                    Renew Subscription
                                </button>
                            </form>
                        @endif

                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
