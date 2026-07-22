<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('System Status & Health') }}</flux:heading>
            <flux:subheading>{{ __('Monitor background queue workers, email services, and automated reminder tasks.') }}</flux:subheading>
        </div>
    </div>

    <!-- Overview Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <flux:card class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ __('Database Engine') }}</span>
                <flux:badge size="sm" color="purple">{{ config('database.default') }}</flux:badge>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ config('app.env') }}</p>
            <p class="text-xs text-zinc-500">PHP v{{ PHP_VERSION }} • Laravel v{{ app()->version() }}</p>
        </flux:card>

        <flux:card class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ __('Queue Driver') }}</span>
                <flux:badge size="sm" color="blue">{{ config('queue.default') }}</flux:badge>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->pendingJobsCount }}</span>
                <span class="text-xs text-zinc-500">{{ __('pending jobs') }}</span>
            </div>
            <p class="text-xs text-zinc-500">{{ $this->failedJobsCount }} {{ __('failed jobs recorded') }}</p>
        </flux:card>

        <flux:card class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ __('Mail System') }}</span>
                <flux:badge size="sm" color="emerald">{{ config('mail.default') }}</flux:badge>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->sentRemindersCount }}</span>
                <span class="text-xs text-zinc-500">{{ __('reminders sent') }}</span>
            </div>
            <p class="text-xs text-zinc-500">{{ $this->pendingRemindersCount }} {{ __('reminders queued for next 24h') }}</p>
        </flux:card>

        <flux:card class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ __('Platform Totals') }}</span>
                <flux:badge size="sm" color="zinc">{{ __('Live') }}</flux:badge>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->totalUsersCount }}</span>
                <span class="text-xs text-zinc-500">{{ __('users') }}</span>
            </div>
            <p class="text-xs text-zinc-500">{{ $this->totalEventsCount }} {{ __('total events managed') }}</p>
        </flux:card>
    </div>

    <!-- Task & Infrastructure Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <flux:card class="space-y-4">
            <flux:heading size="lg">{{ __('Automated Reminder Task Status') }}</flux:heading>
            <flux:separator />

            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Artisan Command') }}</span>
                    <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded font-mono">php artisan app:send-event-reminders</code>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Schedule Interval') }}</span>
                    <span class="font-medium text-xs">{{ __('Every hour (routes/console.php)') }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Pending Event Reminders') }}</span>
                    <flux:badge size="sm" color="{{ $this->pendingRemindersCount > 0 ? 'amber' : 'zinc' }}">
                        {{ $this->pendingRemindersCount }} {{ __('upcoming') }}
                    </flux:badge>
                </div>
            </div>
        </flux:card>

        <flux:card class="space-y-4">
            <flux:heading size="lg">{{ __('Queue Worker Status') }}</flux:heading>
            <flux:separator />

            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Database Jobs Table') }}</span>
                    <span class="font-mono text-xs font-semibold">{{ $this->pendingJobsCount }} {{ __('queued') }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Failed Jobs') }}</span>
                    <flux:badge size="sm" color="{{ $this->failedJobsCount > 0 ? 'red' : 'emerald' }}">
                        {{ $this->failedJobsCount }} {{ __('failures') }}
                    </flux:badge>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Worker Command') }}</span>
                    <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded font-mono">php artisan queue:work</code>
                </div>
            </div>
        </flux:card>
    </div>
</div>
