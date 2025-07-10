<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-gray-900 dark:text-white tracking-tight">
            {{ __('My Transactions') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($invoices->count() > 0)
                <div class="bg-white dark:bg-gray-900 shadow-xl rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 text-sm text-gray-800 dark:text-gray-100">
                            <thead class="bg-indigo-600 dark:bg-indigo-700 text-white text-sm uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-4 text-left font-semibold">Invoice ID</th>
                                    <th class="px-6 py-4 text-left font-semibold">Transaction ID</th>
                                    <th class="px-6 py-4 text-left font-semibold">Amount</th>
                                    <th class="px-6 py-4 text-left font-semibold">Status</th>
                                    <th class="px-6 py-4 text-left font-semibold">Invoice PDF</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach ($invoices as $invoice)
                                    <tr class="hover:bg-indigo-50 dark:hover:bg-indigo-800/40 transition duration-150 ease-in-out">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                            {{ $invoice->id }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                            {{ $invoice->charge ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                            ${{ number_format($invoice->amount_paid / 100, 2) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $status = $invoice->status;
                                                $statusMap = [
                                                    'paid' => [
                                                        'text' => 'Paid',
                                                        'icon' => '💸',
                                                        'color' => 'bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-300'
                                                    ],
                                                    'open' => [
                                                        'text' => 'Open',
                                                        'icon' => '⏳',
                                                        'color' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700/20 dark:text-yellow-300'
                                                    ],
                                                    'uncollectible' => [
                                                        'text' => 'Failed',
                                                        'icon' => '❌',
                                                        'color' => 'bg-red-100 text-red-800 dark:bg-red-800/20 dark:text-red-300'
                                                    ],
                                                    'void' => [
                                                        'text' => 'Voided',
                                                        'icon' => '🚫',
                                                        'color' => 'bg-gray-100 text-gray-700 dark:bg-gray-700/20 dark:text-gray-300'
                                                    ],
                                                ];
                                                $badge = $statusMap[$status] ?? [
                                                    'text' => ucfirst($status),
                                                    'icon' => 'ℹ️',
                                                    'color' => 'bg-gray-100 text-gray-800 dark:bg-gray-700/20 dark:text-gray-200'
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full font-semibold text-xs {{ $badge['color'] }}">
                                                <span class="mr-1">{{ $badge['icon'] }}</span> {{ $badge['text'] }}
                                            </span>
                                            @if ($status === 'uncollectible' && isset($invoice->last_finalization_error))
                                                <div class="text-xs text-red-500 mt-1">
                                                    {{ $invoice->last_finalization_error->message ?? 'Payment failed' }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($invoice->hosted_invoice_url)
                                                <a href="{{ $invoice->hosted_invoice_url }}" target="_blank"
                                                   class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                                                    Download PDF
                                                </a>
                                            @else
                                                <span class="text-gray-400 italic">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-900 shadow-md rounded-xl p-6 mt-6 text-center border border-gray-200 dark:border-gray-700">
                    <p class="text-gray-600 dark:text-gray-300 text-lg">No transactions found yet.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
