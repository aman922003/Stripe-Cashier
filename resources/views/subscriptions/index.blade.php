<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-white leading-tight">
            {{ __('Your Subscriptions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 text-gray-900 dark:text-gray-100">



                    @if (session('success'))
                        <div class="mb-4 bg-green-100 text-green-800 px-4 py-3 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif


                    {{-- Session error --}}
                    @if (session('error'))
                        <div class="mb-4 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 px-4 py-3 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Subscriptions exist --}}
                    @if($subscriptions->count())
                        <div class="grid gap-6">
                            @foreach($stripeSubscriptions as $stripeSub)
                                <a href="{{ route('subscriptions.show', $stripeSub->id) }}" class="block p-6 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-md transition duration-200">

                                    {{-- Header Row --}}
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $stripeSub->items->data[0]->price->nickname ?? 'Subscription Plan' }}
                                        </h3>
                                        <span class="text-sm font-medium px-3 py-1 rounded-full
                                            @if($stripeSub->status === 'active')
                                                bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($stripeSub->status === 'canceled')
                                                bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @elseif($stripeSub->status === 'past_due')
                                                bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @else
                                                bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                            @endif
                                        ">
                                            {{ ucfirst($stripeSub->status) }}
                                        </span>
                                    </div>

                                    {{-- Details Grid --}}
                                    <div class="grid gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <div><strong>Product:</strong> {{ $stripeSub->items->data[0]->price->product->name ?? 'N/A' }}</div>
                                        <div><strong>Amount:</strong> ${{ number_format($stripeSub->items->data[0]->price->unit_amount / 100, 2) }} / {{ $stripeSub->items->data[0]->price->recurring->interval }}</div>
                                        <div><strong>Started:</strong> {{ \Carbon\Carbon::createFromTimestamp($stripeSub->start_date)->toFormattedDateString() }}</div>

                                        @if($stripeSub->cancel_at)
                                            <div><strong>Cancel At:</strong> {{ \Carbon\Carbon::createFromTimestamp($stripeSub->cancel_at)->toFormattedDateString() }}</div>
                                        @endif

                                        @if($stripeSub->trial_end && $stripeSub->trial_end > time())
                                            <div><strong>Trial Ends:</strong> {{ \Carbon\Carbon::createFromTimestamp($stripeSub->trial_end)->toFormattedDateString() }}</div>
                                        @endif

                                        <div><strong>Next Billing:</strong> {{ \Carbon\Carbon::createFromTimestamp($stripeSub->current_period_end)->toFormattedDateString() }}</div>
                                    </div>

                                    {{-- View Details Link --}}
                                    <div class="mt-4 text-sm font-medium text-blue-600 dark:text-blue-400 inline-flex items-center">
                                        View Details
                                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        {{-- Empty State --}}
                        <div class="text-center py-20">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">No Active Subscriptions</h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">You haven't subscribed to a plan yet.</p>
                            <div class="mt-6">
                                <a href="{{ route('pricing') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                                    <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Browse Plans
                                </a>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
