@if (is_impersonating())
    <div class="sticky top-2 z-40 w-full bg-amber-500 dark:bg-amber-600 px-4 py-2.5 flex flex-wrap items-center justify-between gap-3 text-sm font-medium text-amber-950 dark:text-amber-50 shadow-md rounded-lg mb-6">
        <div class="flex items-center gap-2 min-w-0">
            <flux:icon name="eye" class="size-4 shrink-0" />
            <span class="truncate">
                {{ __('You are impersonating') }}
                <strong>{{ auth()->user()->name }}</strong>.
            </span>
        </div>
        <a
            href="{{ route('impersonate.leave') }}"
            class="shrink-0 rounded-md bg-amber-950/20 hover:bg-amber-950/30 dark:bg-amber-50/20 dark:hover:bg-amber-50/30 px-3 py-1 transition-colors text-xs font-semibold"
        >
            {{ __('Leave impersonation') }}
        </a>
    </div>
@endif


