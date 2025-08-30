<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-gray-900 dark:text-white">
            Edit Coupon
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
                <form method="POST" action="{{ route('coupons.update', $coupon->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Name</label>
                        <input type="text" name="name" class="w-full border-gray-300 rounded mt-1"
                               value="{{ old('name', $coupon->name) }}" required>
                        @error('name') <span class="text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Type</label>
                        <select name="type" id="type" class="w-full border-gray-300 rounded mt-1" required>
                            <option value="percentage" {{ $coupon->type === 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="fixed" {{ $coupon->type === 'fixed' ? 'selected' : '' }}>Fixed</option>
                        </select>
                    </div>

                    <div id="percent_off_field" class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Percent Off (%)</label>
                        <input type="number" name="percent_off" class="w-full border-gray-300 rounded mt-1"
                               value="{{ old('percent_off', $coupon->percent_off) }}">
                        @error('percent_off') <span class="text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div id="amount_off_field" class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Amount Off ($)</label>
                        <input type="number" step="0.01" name="amount_off" class="w-full border-gray-300 rounded mt-1"
                               value="{{ old('amount_off', $coupon->amount_off) }}">
                        @error('amount_off') <span class="text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Currencies</label>
                        <input type="text" name="currencies" class="w-full   border-gray-300 rounded mt-1"
                               value="{{ old('currencies', is_array($coupon->currencies) ? implode(',', $coupon->currencies) : '') }}">
                        @error('currencies') <span class="text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Max Redemptions</label>
                        <input type="number" name="max_redemptions" class="w-full border-gray-300 rounded mt-1"
                               value="{{ old('max_redemptions', $coupon->max_redemptions) }}">
                        @error('max_redemptions') <span class="text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Duration</label>
                        <select name="duration" id="duration" class="w-full border-gray-300 rounded mt-1" required>
                            <option value="once" {{ $coupon->duration === 'once' ? 'selected' : '' }}>Once</option>
                            <option value="forever" {{ $coupon->duration === 'forever' ? 'selected' : '' }}>Forever</option>
                            <option value="repeating" {{ $coupon->duration === 'repeating' ? 'selected' : '' }}>Duration in Months</option>
                        </select>
                        @error('duration') <span class="text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4 {{ $coupon->duration === 'repeating' ? '' : 'hidden' }}" id="duration_in_months_container">
                        <label class="block text-gray-700 dark:text-gray-200">Duration in Months</label>
                        <input type="number" name="duration_in_months" class="w-full border-gray-300 rounded mt-1"
                               value="{{ old('duration_in_months', $coupon->duration_in_months) }}" min="1" max="100">
                        @error('duration_in_months') <span class="text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded">
                        Update Coupon
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const typeField = document.getElementById('type');
        const percentField = document.getElementById('percent_off_field');
        const amountField = document.getElementById('amount_off_field');
        const durationField = document.getElementById('duration');
        const durationMonthsContainer = document.getElementById('duration_in_months_container');

        function toggleTypeFields() {
            if (typeField.value === 'percentage') {
                percentField.classList.remove('hidden');
                amountField.classList.add('hidden');
            } else {
                percentField.classList.add('hidden');
                amountField.classList.remove('hidden');
            }
        }

        function toggleDurationField() {
            if (durationField.value === 'repeating') {
                durationMonthsContainer.classList.remove('hidden');
            } else {
                durationMonthsContainer.classList.add('hidden');
            }
        }

        typeField.addEventListener('change', toggleTypeFields);
        durationField.addEventListener('change', toggleDurationField);

        // Run on page load
        toggleTypeFields();
        toggleDurationField();
    </script>
</x-app-layout>
