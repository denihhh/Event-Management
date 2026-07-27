<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Review Organizer Requests') }}</flux:heading>
            <flux:subheading>{{ __('Evaluate partner organization credentials and set organizer role durations.') }}
            </flux:subheading>
        </div>
    </div>

    <!-- Filter toolbar -->
    <div class="flex gap-4 border-b border-zinc-200 dark:border-zinc-700 text-sm font-medium">
        <button type="button" wire:click="$set('filter', 'pending')"
            class="pb-3 border-b-2 transition-colors cursor-pointer {{ $filter === 'pending' ? 'border-purple-600 text-purple-700 dark:border-purple-400 dark:text-purple-400 font-bold' : 'border-transparent text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}">
            {{ __('Pending Review') }}
        </button>
        <button type="button" wire:click="$set('filter', 'approved')"
            class="pb-3 border-b-2 transition-colors cursor-pointer {{ $filter === 'approved' ? 'border-purple-600 text-purple-700 dark:border-purple-400 dark:text-purple-400 font-bold' : 'border-transparent text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}">
            {{ __('Approved') }}
        </button>
        <button type="button" wire:click="$set('filter', 'rejected')"
            class="pb-3 border-b-2 transition-colors cursor-pointer {{ $filter === 'rejected' ? 'border-purple-600 text-purple-700 dark:border-purple-400 dark:text-purple-400 font-bold' : 'border-transparent text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}">
            {{ __('Rejected') }}
        </button>
        <button type="button" wire:click="$set('filter', 'all')"
            class="pb-3 border-b-2 transition-colors cursor-pointer {{ $filter === 'all' ? 'border-purple-600 text-purple-700 dark:border-purple-400 dark:text-purple-400 font-bold' : 'border-transparent text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}">
            {{ __('All Applications') }}
        </button>
    </div>

    @if ($applications->isEmpty())
        <x-ui.empty-state
            icon="clipboard-document-check"
            :title="__('No applications found')"
            :description="__('No organizer requests match the selected status filter.')"
        />
    @else
        <div
            class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700/60 bg-white dark:bg-zinc-800/50 shadow-xs">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column class="px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Applicant / Organization') }}
                    </flux:table.column>
                    <flux:table.column class="px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Proposed Event Details') }}
                    </flux:table.column>
                    <flux:table.column class="w-28 whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Status') }}
                    </flux:table.column>
                    <flux:table.column align="end" class="w-32 whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">
                        {{ __('Actions') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($applications as $app)
                        <flux:table.row wire:key="app-{{ $app->id }}">
                            <flux:table.cell class="px-4 py-3.5 first:pl-6 last:pr-6">
                                <div class="min-w-0 py-0.5 space-y-1">
                                    <p class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $app->organization_name ?: $app->user->name }}
                                    </p>
                                    @if ($app->business_reg_number)
                                        <p class="text-xs text-purple-600 dark:text-purple-400 font-mono">Reg:
                                            {{ $app->business_reg_number }}</p>
                                    @endif
                                    <p class="text-xs text-zinc-500 truncate">{{ $app->user->name }} ({{ $app->user->email }})
                                    </p>
                                    <p class="text-[10px] text-zinc-400">Submitted {{ $app->created_at->diffForHumans() }}</p>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="px-4 py-3.5 first:pl-6 last:pr-6">
                                <div class="space-y-1 max-w-xs">
                                    <p class="font-semibold text-sm">{{ $app->event_title }}</p>
                                    <p class="text-xs text-zinc-500 line-clamp-2">{{ $app->event_description }}</p>
                                    <p class="text-[11px] text-purple-600 dark:text-purple-400">Proposed:
                                        {{ $app->proposed_starts_at->format('M j, Y \a\t g:i A') }}</p>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">
                                @if ($app->isPending())
                                    <flux:badge color="amber" rounded size="sm">{{ __('Pending') }}</flux:badge>
                                @elseif ($app->isApproved())
                                    <flux:badge color="emerald" rounded size="sm">{{ __('Approved') }}</flux:badge>
                                @else
                                    <flux:badge color="red" rounded size="sm">{{ __('Rejected') }}</flux:badge>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell align="end" class="px-4 py-3.5 first:pl-6 last:pr-6">
                                <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                    <flux:button size="sm" variant="subtle" icon="pencil-square"
                                        wire:click="reviewApplication({{ $app->id }})">
                                        {{ __('Review') }}
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>

        <div class="pt-4">
            {{ $applications->links() }}
        </div>
    @endif

    {{-- Review Modal --}}
    <flux:modal name="review-application-modal" class="space-y-6 backdrop:backdrop-blur-sm backdrop:bg-black/50">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Review Partner Application') }}</flux:heading>
                <flux:subheading>{{ __('Verify business credentials and approve organizer status.') }}</flux:subheading>
            </div>

            @if ($this->activeApplication)
                <div
                    class="p-4 bg-zinc-50 dark:bg-zinc-800/80 rounded-xl border border-zinc-200 dark:border-zinc-700/60 space-y-3 text-xs">
                    <div class="font-semibold text-sm text-zinc-900 dark:text-zinc-100 flex items-center justify-between">
                        <span>{{ $this->activeApplication->organization_name ?: $this->activeApplication->user->name }}</span>
                        <flux:badge size="sm" color="purple">
                            {{ $this->activeApplication->business_reg_number ?: 'Personal' }}</flux:badge>
                    </div>

                    <div
                        class="grid grid-cols-2 gap-2 text-zinc-600 dark:text-zinc-400 border-t border-zinc-200 dark:border-zinc-700 pt-2">
                        <div>
                            <span class="text-zinc-400 block font-medium">Applicant:</span>
                            {{ $this->activeApplication->user->name }} ({{ $this->activeApplication->user->email }})
                        </div>
                        <div>
                            <span class="text-zinc-400 block font-medium">Contact Phone:</span>
                            {{ $this->activeApplication->contact_phone ?: 'N/A' }}
                        </div>
                        <div>
                            <span class="text-zinc-400 block font-medium">Payout Bank:</span>
                            {{ $this->activeApplication->bank_name ?: 'N/A' }}
                        </div>
                        <div>
                            <span class="text-zinc-400 block font-medium">Bank Account #:</span>
                            <span class="font-mono">{{ $this->activeApplication->bank_account_number ?: 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2">
                        <span class="text-zinc-400 block font-medium">Proposed Event:</span>
                        <p class="font-medium text-zinc-800 dark:text-zinc-200 mt-0.5">
                            {{ $this->activeApplication->event_title }}</p>
                        <p class="text-zinc-500 mt-0.5 line-clamp-2">{{ $this->activeApplication->event_description }}</p>
                    </div>
                </div>
            @endif

            @if ($this->activeApplication && $this->activeApplication->isPending())
                <div class="space-y-4">
                    <flux:textarea wire:model="rejectionNotes" :label="__('Rejection Notes / Feedback (Optional)')"
                        :placeholder="__('Provide notes if rejecting request...')" rows="3" />
                </div>

                <div class="flex justify-between items-center gap-3 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    <div class="flex justify-start">


                        <flux:modal.close>
                            <flux:button variant="subtle">
                                {{ __('Cancel') }}
                            </flux:button>
                        </flux:modal.close>
                    </div>

                    <div class="flex justify-end gap-3">
                        <flux:button variant="danger" wire:click="rejectApplication" wire:loading.attr="disabled">
                            {{ __('Reject Request') }}
                        </flux:button>
                        <flux:button variant="primary" wire:click="approveApplication" wire:loading.attr="disabled">
                            {{ __('Approve & Grant Role') }}
                        </flux:button>
                    </div>
                </div>
            @else
                <div class="p-4 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-sm flex items-center justify-between">
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">{{ __('Application Status:') }}</span>
                    @if ($this->activeApplication?->isApproved())
                        <flux:badge color="emerald" rounded size="sm">{{ __('Approved') }}</flux:badge>
                    @else
                        <flux:badge color="red" rounded size="sm">{{ __('Rejected') }}</flux:badge>
                    @endif
                </div>

                @if ($this->activeApplication?->admin_notes)
                    <div
                        class="p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800/50 rounded-lg text-xs space-y-1">
                        <span class="font-semibold text-red-800 dark:text-red-300">{{ __('Admin Notes:') }}</span>
                        <p class="text-red-700 dark:text-red-400 italic">"{{ $this->activeApplication->admin_notes }}"</p>
                    </div>
                @endif

                <div class="flex justify-end pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:modal.close>
                        <flux:button variant="subtle">
                            {{ __('Close') }}
                        </flux:button>
                    </flux:modal.close>
                </div>
            @endif
        </div>
    </flux:modal>
</div>