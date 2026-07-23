<?php

namespace App\Livewire\Organizer;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrganizerAnalytics extends Component
{
    /**
     * Get analytics summary data for the authenticated organizer.
     *
     * @return array<string, mixed>
     */
    public function getAnalyticsData(): array
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return [
                'totalEvents' => 0,
                'totalTicketsSold' => 0,
                'totalAttendees' => 0,
                'ticketsGrowth' => 0,
                'monthlySales' => array_fill(0, 12, 0),
                'monthlyAttendees' => array_fill(0, 12, 0),
                'monthLabels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            ];
        }

        $eventQuery = $user->isAdmin() ? Event::query() : $user->organizedEvents();
        $eventIds = $eventQuery->pluck('id');

        $totalEvents = $eventQuery->count();
        $totalTicketsSold = Ticket::whereIn('event_id', $eventIds)->count();
        $totalAttendees = Ticket::whereIn('event_id', $eventIds)->distinct('user_id')->count('user_id');

        // Growth calculations (Current month vs Previous month)
        $currentMonthCount = Ticket::whereIn('event_id', $eventIds)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $previousMonth = now()->subMonth();
        $previousMonthCount = Ticket::whereIn('event_id', $eventIds)
            ->whereYear('created_at', $previousMonth->year)
            ->whereMonth('created_at', $previousMonth->month)
            ->count();

        $ticketsGrowth = $previousMonthCount > 0
            ? round((($currentMonthCount - $previousMonthCount) / $previousMonthCount) * 100, 1)
            : ($currentMonthCount > 0 ? 100 : 0);

        // Monthly dataset for current year (Jan - Dec)
        $year = now()->year;
        $monthlySales = [];
        $monthlyAttendees = [];
        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        for ($month = 1; $month <= 12; $month++) {
            $salesCount = Ticket::whereIn('event_id', $eventIds)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $attendeeCount = Ticket::whereIn('event_id', $eventIds)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->distinct('user_id')
                ->count('user_id');

            $monthlySales[] = $salesCount;
            $monthlyAttendees[] = $attendeeCount;
        }

        return [
            'totalEvents' => $totalEvents,
            'totalTicketsSold' => $totalTicketsSold,
            'totalAttendees' => $totalAttendees,
            'ticketsGrowth' => $ticketsGrowth,
            'monthlySales' => $monthlySales,
            'monthlyAttendees' => $monthlyAttendees,
            'monthLabels' => $monthLabels,
        ];
    }

    public function render(): View
    {
        return view('livewire.organizer.organizer-analytics', $this->getAnalyticsData());
    }
}
