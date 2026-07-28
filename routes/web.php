<?php

use App\Http\Controllers\TicketPdfController;
use App\Http\Middleware\EnsureUserIsOrganizer;
use App\Livewire\Admin\ReviewApplications;
use App\Livewire\Admin\SystemEvents;
use App\Livewire\Admin\SystemStatus;
use App\Livewire\Admin\Users;
use App\Livewire\Events\EventIndex;
use App\Livewire\Events\EventShow;
use App\Livewire\Organizer\CheckInAttendees;
use App\Livewire\Organizer\EventAttendees;
use App\Livewire\Organizer\EventCreate;
use App\Livewire\Organizer\EventEdit;
use App\Livewire\Organizer\GlobalAttendees;
use App\Livewire\Organizer\OrganizerEventIndex;
use App\Livewire\Settings\BecomeOrganizer;
use App\Livewire\Tickets\MyTickets;
use App\Livewire\Welcome;
use Illuminate\Support\Facades\Route;

Route::get('/', Welcome::class)->name('home');
Route::get('/events', EventIndex::class)->name('events.index');
Route::get('/events/{event}', EventShow::class)->name('events.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('/my-tickets', MyTickets::class)->name('tickets.index');
    Route::get('/tickets/{ticket}/pdf', [TicketPdfController::class, 'download'])->name('tickets.pdf');
    Route::get('/event-operation/become-organizer', BecomeOrganizer::class)->name('become-organizer.edit');
});

// Organizer routes
Route::middleware(['auth', 'verified', EnsureUserIsOrganizer::class])->group(function () {
    Route::get('/organizer/events', OrganizerEventIndex::class)->name('organizer.events.index');
    Route::get('/organizer/events/create', EventCreate::class)->name('organizer.events.create');
    Route::get('/organizer/events/{event}/edit', EventEdit::class)->name('organizer.events.edit');
    Route::get('/organizer/events/{event}/attendees', EventAttendees::class)->name('organizer.events.attendees');
    Route::get('/organizer/events/{event}/check-in', CheckInAttendees::class)->name('organizer.events.check-in');
    Route::get('/organizer/attendees', GlobalAttendees::class)->name('organizer.attendees.index');
});

// Admin routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/users', Users::class)->name('admin.users.index');
    Route::get('/admin/applications', ReviewApplications::class)->name('admin.organizer-applications.index');
    Route::get('/admin/events', SystemEvents::class)->name('admin.events.index');
    Route::get('/admin/status', SystemStatus::class)->name('admin.system.status');
});

Route::middleware('auth')->group(function () {
    Route::impersonate();
});

require __DIR__.'/settings.php';
