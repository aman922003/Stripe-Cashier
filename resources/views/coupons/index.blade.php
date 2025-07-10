<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-3xl text-gray-900 dark:text-white">
                {{ __('All Coupons') }}
            </h2>
            <a href="{{ route('coupons.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg">
                + Create Coupon
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount/Percent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                        @forelse ($coupons as $coupon)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $coupon->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $coupon->code ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $coupon->type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($coupon->type === 'percentage')
                                        {{ $coupon->percent_off }}%
                                    @else
                                        ${{ $coupon->amount_off }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                    <a href="{{ route('coupons.edit', $coupon->id) }}"
                                       class="inline-flex items-center px-3 py-1 bg-blue-500 text-white rounded">
                                        Edit
                                    </a>
                                    <a href="{{ route('coupons.assign.form', $coupon->id) }}"
                                        class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded">
                                            Assign
                                    </a>
                                    <form action="{{ route('coupons.destroy', $coupon->id) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this coupon?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-1 bg-red-600 text-white rounded">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center">No coupons found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $coupons->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
