<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-gray-900 dark:text-white">
            Assign Coupon: {{ $coupon->name }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8">

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- SEARCH FORM --}}
        <form
            action="{{ route('coupons.assign.form', $coupon->id) }}"
            method="GET"
            class="mb-6"
        >
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search users by name or email..."
                    class="w-full p-2 border border-gray-300 rounded"
                >
                <button
                    type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700"
                >
                    Search
                </button>
            </div>
        </form>

        {{-- ASSIGN COUPON FORM --}}
        <form
            action="{{ route('coupons.assign', $coupon->id) }}"
            method="POST"
        >
            @csrf

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">

                {{-- SELECT ALL CHECKBOX --}}
                <div class="flex items-center mb-4">
                    <input
                        type="checkbox"
                        id="selectAll"
                        class="mr-2"
                    >
                    <label for="selectAll" class="font-semibold">Select All</label>
                </div>

                {{-- USER LIST --}}
                <div class="max-h-96 overflow-y-auto border rounded p-4">
                    @forelse($users as $user)
                        <label class="flex items-center mb-2">
                            <input
                                type="checkbox"
                                name="user_ids[]"
                                value="{{ $user->id }}"
                                class="mr-2 user-checkbox"
                            >
                            <span>{{ $user->name }} ({{ $user->email }})</span>
                        </label>
                    @empty
                        <p class="text-gray-600">No users found.</p>
                    @endforelse
                </div>

                {{-- ASSIGN BUTTON --}}
                <button
                    type="submit"
                    class="mt-6 inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700"
                >
                    Assign Coupon
                </button>
            </div>
        </form>

        {{-- SEND TO FRIEND FORM --}}
        <div class="mt-12 bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Send this coupon to a friend</h3>

            <form
                action="{{ route('coupons.send.to.friend', $coupon->id) }}"
                method="POST"
                class="flex flex-col gap-4"
            >
                @csrf
                <input
                    type="email"
                    name="friend_email"
                    placeholder="Friend's email address"
                    required
                    class="w-full p-2 border border-gray-300 rounded"
                >
                <button
                    type="submit"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700"
                >
                    Send Coupon
                </button>
            </form>
        </div>
    </div>

    {{-- SELECT ALL SCRIPT --}}
    <script>
        document.getElementById('selectAll').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
</x-app-layout>
