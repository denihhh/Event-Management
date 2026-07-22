# Project Overview: Event Management & Ticketing System (`eventive`)

## 1. Executive Summary
A web application designed for event organizers to publish public events with fixed ticket capacities and for users to discover, register, and receive custom digital tickets via email with automated reminders. Features multi-role access control (Attendee, Organizer, Admin), attendee-to-organizer request workflows with time-bound role duration, admin impersonation, and atomic pessimistic concurrency protection during ticket checkout.

---

## 2. Tech Stack & Environment
- **Framework:** Laravel 13 (PHP 8.4)
- **Authentication:** Laravel Fortify (Backend Auth) & Laravel Breeze with Livewire v4 (Class-Based Components) & Flux UI v2
- **Styling:** Tailwind CSS v4
- **Testing:** Pest PHP v4
- **Database:** MySQL (Production) / SQLite (Local testing)
- **Queue System:** Database Driver (`QUEUE_CONNECTION=database`)
- **Mail:** Laravel Mailable (`EventTicketMail`, `EventReminderMail`, `EventUpdatedMail`, `EventCancelledMail`) via SMTP
- **User Impersonation:** `lab404/laravel-impersonate` v1.7
- **AI Tooling Integration:** Laravel Boost MCP Server

---

## 3. Core Domain Models & Relationships

### Users (`users`)
- **Roles:**
  - **Admin** (`is_admin = true`): System superuser. Bypasses all Gate/Policy checks via `Gate::before()`, reviews organizer applications, impersonates users, and manages system settings. Cannot be impersonated.
  - **Organizer** (`is_organizer = true`): Can publish, edit, and cancel events, view attendee rosters, and export CSV reports.
  - **Attendee**: Browses public events, claims digital tickets, views ticket passes, and applies for organizer privileges.
- **Attributes:** `id`, `name`, `email`, `password`, `is_organizer`, `organizer_expires_at` (datetime, nullable), `is_admin`, `timestamps`.

### Organizer Applications (`organizer_applications`)
- **Belongs to:** User (`user()`)
- **Attributes:** `id`, `user_id`, `organization_name`, `business_reg_number`, `contact_phone`, `bank_name`, `bank_account_number`, `event_title`, `event_description`, `proposed_starts_at`, `status` (`pending`, `approved`, `rejected`), `admin_notes`, `timestamps`.

### Events (`events`)
- **Belongs to:** User (Organizer/Admin) (`organizer()`)
- **Has many:** Tickets (`tickets()`)
- **Soft Deletes:** `SoftDeletes` trait (`deleted_at`).
- **Attributes:** `id`, `user_id`, `title`, `description`, `starts_at`, `location`, `capacity`, `tickets_sold`, `created_at`, `updated_at`, `deleted_at`.

### Tickets (`tickets`)
- **Belongs to:** User (`user()`), Event (`event()`)
- **Attributes:** `id`, `event_id`, `user_id`, `ticket_code` (UUID string), `reminder_sent` (boolean), `timestamps`.

---

## 4. Key Functional Features & Architecture

### A. Event Management (Organizers & Admins)
- Create, edit, publish, and soft-delete public events.
- Capacity enforcement with real-time remaining ticket counts.
- Attendee roster viewing with real-time search and streaming CSV export (`GlobalAttendees` at `/organizer/attendees`).

### B. Registration & Digital Ticketing (Attendees)
- Public event discovery with search and date filters (`upcoming`, `this_week`, `this_month`, `past`).
- One-click ticket registration.
- **Concurrency Prevention:** `DB::transaction()` with pessimistic database locking (`lockForUpdate()`) on the `Event` record to guarantee capacity is never exceeded.
- Dispatch queued email (`EventTicketMail`) containing digital ticket credentials upon ticket creation (dispatched after database transaction commit).

### C. Organizer Request & Approval Workflow
- Attendees apply to become an organizer via `/event-operation/become-organizer` (`route('become-organizer.edit')`) or via Module 2 "Apply Organizer".
- Applicants fill in business entity credentials (Company Name, Registration No., Phone, Bank Payout info) and proposed event details.
- Admins review pending applications via `/admin/applications` (`App\Livewire\Admin\ReviewApplications`) and grant permanent organizer access upon approval.
- `User::isOrganizer()` returns `$user->is_organizer` as the single source of truth used across middleware (`EnsureUserIsOrganizer`) and policies (`EventPolicy`).

### D. User Impersonation & User Management
- Housed under `App\Livewire\Admin\Users` at `/admin/users` (`route('admin.users.index')`).
- Admins impersonate non-admin users via `lab404/laravel-impersonate` routes (`GET /impersonate/take/{id}`).
- Persistent top warning banner (`partials/impersonation-banner.blade.php`) displays active impersonation state and exit option (`GET /impersonate/leave`).
- Admin Gate superpower: `Gate::before()` grants admins blanket approval over all policy checks.

### E. System Health & Master Audit Tools (Admins)
- **Event Audit** (`App\Livewire\Admin\SystemEvents` at `/admin/events`): Platform-wide audit table of all active, past, and soft-deleted events with restore/cancel capabilities.
- **System Status** (`App\Livewire\Admin\SystemStatus` at `/admin/status`): Infrastructure monitoring for database, queue worker counts, and automated reminder statistics.

### F. Navigation & Sidebar Architecture
- **Module 1: Main** (`Dashboard`, `Browse Events`, `My Tickets`).
- **Module 2: Event Operations** (`Managed Events`, `Attendee Roster`, `Apply Organizer` for Attendees, `Review Requests` for Admins).
- **Module 3: System Admin** (`User Management` at `/admin/users`, `Event Audit` at `/admin/events`, `System Status` at `/admin/status` — guarded strictly for Admins and hidden during active impersonation).
- **Settings**: Single item under Bottom Desktop User Profile Menu (`route('profile.edit')`).

### G. Automated Reminders
- Custom Artisan command (`app:send-event-reminders`) scheduled via `routes/console.php`.
- Evaluates upcoming events starting within 24 hours where `reminder_sent` is `false`.
- Queues reminder emails (`EventReminderMail`) to registered attendees and sets `reminder_sent = true`.

---

## 5. Business Rules & Technical Constraints
1. **Capacity Guarantee:** Ticket registrations must use pessimistic database locking (`lockForUpdate()`) inside transactions to handle concurrent purchases safely.
2. **Asynchronous Execution:** All mail notifications implement `ShouldQueue` and execute outside uncommitted transaction boundaries.
3. **Route & Role Security:** Event creation/management routes require authenticated organizer or admin access via `EnsureUserIsOrganizer` middleware and policies.
4. **Impersonation Loop Safety:** Admins can impersonate non-admins (`canImpersonate() = true`), but admins can NEVER be impersonated (`canBeImpersonated() = false`).
5. **Testing Standards:** All feature and unit tests are written using Pest PHP (`test()` / `it()` syntax).
6. **Component Structure:** Use standard Class-based Livewire components (`app/Livewire/*` + `resources/views/livewire/*`) and Flux UI components.