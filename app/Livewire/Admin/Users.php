<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('User Management')]
class Users extends Component
{
    use WithPagination;

    public string $search = '';

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
     * All users matching current search excluding authenticated admin.
     *
     * @return LengthAwarePaginator<int, User>
     */
    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->where('id', '!=', Auth::id())
            ->when($this->search, function ($query): void {
                $query->where(function ($q): void {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('name')
            ->paginate(15);
    }

    public function render(): View
    {
        return view('livewire.admin.users');
    }
}
