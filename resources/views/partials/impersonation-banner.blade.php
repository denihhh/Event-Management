@if (is_impersonating())
    <div class="sticky top-0 z-50 w-full bg-amber-500 dark:bg-amber-600 px-4 py-2.5 flex items-center justify-between gap-4 text-sm font-medium text-amber-950 dark:text-amber-50 shadow-sm">
        <div class="flex items-center gap-2">
            <flux:icon name="eye" class="size-4 shrink-0" />
            <span>
                {{ __('You are impersonating') }}
                <strong>{{ auth()->user()->name }}</strong>.
            </span>
        </div>
        <a
            href="{{ route('impersonate.leave') }}"
            class="shrink-0 rounded-md bg-amber-950/10 hover:bg-amber-950/20 dark:bg-amber-50/10 dark:hover:bg-amber-50/20 px-3 py-1 transition-colors"
        >
            {{ __('Leave impersonation') }}
        </a>
    </div>
@endif
