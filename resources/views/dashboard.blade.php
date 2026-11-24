<x-app-layout>
    <x-slot name="header">
        <h2 class="font-medium text-lg text-slate-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-200 overflow-hidden shadow-xs sm:rounded-xl">
                <div class="p-6 text-slate-700 text-sm font-normal">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
