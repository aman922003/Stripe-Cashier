{{-- resources/views/coupons/assigned_users.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-3xl text-gray-900 dark:text-white">
                All Assigned Coupons & Users
            </h2>
            <a href="{{ route('coupons.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">
                + Create Coupon
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($users->count())
            <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow rounded-lg">
                <table class="min-w-full">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Assigned Coupons</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $user->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->coupons->count())
                                        <ul class="space-y-2">
                                            @foreach($user->coupons as $coupon)
                                                <li class="flex justify-between items-center bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded px-4 py-2">
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-semibold text-indigo-700 dark:text-indigo-400">
                                                            {{ $coupon->name }}
                                                        </span>
                                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-indigo-200 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                                            {{ $coupon->type === 'percentage' ? $coupon->percent_off.'% Off' : '$'.$coupon->amount_off.' Off' }}
                                                        </span>
                                                    </div>
                                                    <form method="POST"
                                                          action="{{ route('coupons.unassign', ['couponId' => $coupon->id, 'userId' => $user->id]) }}"
                                                          onsubmit="return confirm('Are you sure you want to unassign this coupon?');">
                                                        @csrf
                                                        <button type="submit"
                                                                class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs font-semibold rounded hover:bg-red-700 transition">
                                                            Unassign
                                                        </button>
                                                    </form>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="italic text-sm text-gray-500 dark:text-gray-400">No coupons assigned</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-16">
                <p class="text-lg text-gray-500 dark:text-gray-400">No users have been assigned any coupons yet.</p>
            </div>
        @endif
    </div>
</x-app-layout>
