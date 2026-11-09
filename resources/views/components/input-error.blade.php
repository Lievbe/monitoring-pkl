@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'mt-2 text-xs font-medium text-red-600 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-center gap-1.5">
                <svg class="h-3.5 w-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                {{ $message }}
            </li>
        @endforeach
    </ul>
@endif
