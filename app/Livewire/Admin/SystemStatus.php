<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('System Status')]
class SystemStatus extends Component
{
    public function mount(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);
    }

    #[Computed]
    public function pendingJobsCount(): int
    {
        try {
            return DB::table('jobs')->count();
        } catch (\Throwable) {
            return 0;
        }
    }

    #[Computed]
    public function failedJobsCount(): int
    {
        try {
            return DB::table('failed_jobs')->count();
        } catch (\Throwable) {
            return 0;
        }
    }

    #[Computed]
    public function pendingRemindersCount(): int
    {
        return Ticket::where('reminder_sent', false)
            ->whereHas('event', function ($q): void {
                $q->where('starts_at', '>=', now())
                    ->where('starts_at', '<=', now()->addHours(24));
            })
            ->count();
    }

    #[Computed]
    public function sentRemindersCount(): int
    {
        return Ticket::where('reminder_sent', true)->count();
    }

    #[Computed]
    public function totalUsersCount(): int
    {
        return User::count();
    }

    #[Computed]
    public function totalEventsCount(): int
    {
        return Event::withTrashed()->count();
    }

    public function render(): View
    {
        return view('livewire.admin.system-status');
    }
}
