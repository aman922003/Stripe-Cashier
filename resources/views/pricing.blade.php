<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-900 dark:text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <section class="rounded-xl">
                <div class="py-12 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
                    <div class="text-center max-w-2xl mx-auto mb-14">
                        <h2 class="text-4xl font-extrabold text-indigo-700 dark:text-indigo-400 mb-4">
                            Choose Your Plan
                        </h2>
                        <p class="text-lg text-gray-600 dark:text-gray-300 font-light">
                            Flexible and transparent pricing for developers, teams, and businesses of all sizes.
                        </p>
                    </div>

                    <div class="grid gap-10 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($plans as $plan)
                            <div class="flex flex-col justify-between bg-gradient-to-br {{ $plan->color ?? 'from-gray-100 to-gray-50 border-gray-200' }} dark:bg-gray-800 border rounded-2xl shadow-xl hover:shadow-2xl transition-shadow duration-300">
                                <div class="p-8">
                                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">{{ $plan->name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">{{ $plan->desc }}</p>
                                    <div class="flex justify-center items-baseline mb-8">
                                        <span class="text-5xl font-extrabold text-indigo-700 dark:text-indigo-300 mr-2">
                                            ${{ number_format($plan->price / 100, 2) }}
                                        </span>
                                        <span class="text-gray-600 dark:text-gray-400 text-base">/day</span>
                                    </div>
                                    <ul class="space-y-4 text-sm text-left">
                                        @foreach (json_decode($plan->features) as $feature)
                                            <li class="flex items-start space-x-3">
                                                <svg class="w-5 h-5 text-green-500 dark:text-green-400 flex-shrink-0 mt-1"
                                                     fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0L4 10a1 1 0 011.414-1.414L8 11.586l7.293-7.293a1 1 0 011.414 0z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                                <span class="text-gray-700 dark:text-gray-300">{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="p-6">
                                    <a href="{{ route('checkout', $plan->name) }}"
                                       class="w-full inline-block text-center text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 dark:focus:ring-indigo-800 font-medium rounded-lg text-sm px-5 py-3 transition">
                                        Get started
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
