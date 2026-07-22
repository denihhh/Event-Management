<div class="space-y-6">
    <div>
        <flux:heading size="xl" level="1">{{ __('Apply for Organizer Status') }}</flux:heading>
        <flux:subheading>{{ __('Submit your business credentials and event proposal for admin review.') }}</flux:subheading>
    </div>

    @if (auth()->user()->isOrganizer())
        <div class="p-6 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-xl space-y-3 max-w-3xl">
            <div class="flex items-center gap-2 text-emerald-800 dark:text-emerald-300 font-semibold">
                <flux:icon name="check-circle" class="size-5" />
                <span>{{ __('Active Organizer Account') }}</span>
            </div>
            <p class="text-sm text-emerald-700 dark:text-emerald-400">
                {{ __('Your account currently has organizer privileges.') }}
            </p>
            <flux:button size="sm" variant="primary" :href="route('organizer.events.index')" wire:navigate>
                {{ __('Go to Organizer Dashboard') }}
            </flux:button>
        </div>
    @else
        @if ($this->latestApplication && $this->latestApplication->isPending())
            <div class="p-6 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 rounded-xl space-y-3 mb-6 max-w-3xl">
                <div class="flex items-center gap-2 text-amber-800 dark:text-amber-300 font-semibold">
                    <flux:icon name="clock" class="size-5" />
                    <span>{{ __('Application Under Review') }}</span>
                </div>
                <p class="text-sm text-amber-700 dark:text-amber-400">
                    {{ __('Your request for') }} <strong>"{{ $this->latestApplication->organization_name ?: $this->latestApplication->event_title }}"</strong> {{ __('was submitted on') }} {{ $this->latestApplication->created_at->format('M j, Y') }} {{ __('and is currently pending admin verification.') }}
                </p>
            </div>
        @elseif ($this->latestApplication && $this->latestApplication->isRejected())
            <div class="p-6 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/60 rounded-xl space-y-3 mb-6 max-w-3xl">
                <div class="flex items-center gap-2 text-red-800 dark:text-red-300 font-semibold">
                    <flux:icon name="x-circle" class="size-5" />
                    <span>{{ __('Previous Application Not Approved') }}</span>
                </div>
                <p class="text-sm text-red-700 dark:text-red-400">
                    {{ __('Your previous request was not approved.') }}
                    @if ($this->latestApplication->admin_notes)
                        <br><span class="italic">"{{ $this->latestApplication->admin_notes }}"</span>
                    @endif
                </p>
                <p class="text-xs text-red-600 dark:text-red-400">
                    {{ __('You may submit an updated application below.') }}
                </p>
            </div>
        @endif

        <form wire:submit="submitApplication" class="space-y-8 max-w-3xl">
            {{-- Section 1: Business & Contact Verification --}}
            <div class="bg-white dark:bg-zinc-800/60 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700/60 space-y-6">
                <div>
                    <flux:heading size="lg" icon="building-office-2">{{ __('Business & Organization Information') }}</flux:heading>
                    <flux:subheading>{{ __('Provide legal registration and contact details for partner verification.') }}</flux:subheading>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input
                        wire:model="organization_name"
                        :label="__('Company / Organization Name')"
                        :placeholder="__('e.g. Apex Sports & Events Sdn Bhd')"
                        required
                    />

                    <flux:input
                        wire:model="business_reg_number"
                        :label="__('SSM / Business Registration No.')"
                        :placeholder="__('e.g. 202401099881 (1234567-A)')"
                        required
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <flux:input
                        wire:model="contact_phone"
                        :label="__('Contact Phone Number')"
                        :placeholder="__('e.g. +60 12-345 6789')"
                        required
                    />

                    <flux:input
                        wire:model="bank_name"
                        :label="__('Bank Name')"
                        :placeholder="__('e.g. Maybank / CIMB')"
                        required
                    />

                    <flux:input
                        wire:model="bank_account_number"
                        :label="__('Bank Account Number')"
                        :placeholder="__('e.g. 514012345678')"
                        required
                    />
                </div>
            </div>

            {{-- Section 2: Initial Event Proposal --}}
            <div class="bg-white dark:bg-zinc-800/60 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700/60 space-y-6">
                <div>
                    <flux:heading size="lg" icon="calendar">{{ __('Proposed Event Details') }}</flux:heading>
                    <flux:subheading>{{ __('Outline the first event you intend to host on the platform.') }}</flux:subheading>
                </div>

                <flux:input
                    wire:model="event_title"
                    :label="__('Proposed Event Title')"
                    :placeholder="__('e.g. City Half Marathon & Fun Run 2026')"
                    required
                />

                <flux:textarea
                    wire:model="event_description"
                    :label="__('Event Description & Target Audience')"
                    :placeholder="__('Explain event goals, runner categories, target participants, etc.')"
                    rows="4"
                    required
                />

                <div>
                    <flux:input
                        wire:model="proposed_starts_at"
                        type="datetime-local"
                        :label="__('Proposed Event Date')"
                        required
                    />
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-4">
                <flux:button variant="primary" type="submit" wire:loading.attr="disabled" class="px-8">
                    {{ __('Submit Application for Admin Review') }}
                </flux:button>
            </div>
        </form>
    @endif
</div>
