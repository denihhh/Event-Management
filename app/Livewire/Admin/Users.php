<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('User Management')]
class Users extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $role = 'all';

    /**
     * Gate this page to admins only.
     */
    public function mount(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);
    }

    /**
     * Reset pagination when search query changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when role filter changes.
     */
    public function updatedRole(): void
    {
        $this->resetPage();
    }

    /**
     * Start impersonating the given user and redirect to dashboard.
     */
    public function impersonate(int $userId): void
    {
        $target = User::findOrFail($userId);

        abort_unless($target->canBeImpersonated(), 403);

        Auth::user()->impersonate($target);

        $this->redirect(route('dashboard'), navigate: true);
    }

    /**
     * All users matching current search and role filter excluding authenticated admin.
     *
     * @return LengthAwarePaginator<int, User>
     */
    #[Computed]
    public function users(): LengthAwarePaginator
    {
        $query = User::query()
            ->where('id', '!=', Auth::id())
            ->when($this->search, function ($query): void {
                $query->where(function ($q): void {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            });

        match ($this->role) {
            'admin' => $query->where('is_admin', true),
            'organizer' => $query->where('is_organizer', true)->where('is_admin', false),
            'attendee' => $query->where('is_organizer', false)->where('is_admin', false),
            default => null,
        };

        return $query->orderBy('name')->paginate(15);
    }

    public function render(): View
    {
        return view('livewire.admin.users');
    }
}
