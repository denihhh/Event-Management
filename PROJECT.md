# Project Overview: Event Management & Ticketing System (`eventive`)

## 1. Executive Summary
A web application designed for event organizers to publish public events with fixed ticket capacities and for users to discover, register, and receive custom digital tickets via email with automated reminders. Features multi-role access control (Attendee, Organizer, Admin), attendee-to-organizer request workflows with time-bound role duration, admin impersonation, and atomic pessimistic concurrency protection during ticket checkout.

---

## 2. Tech Stack & Environment
- **Framework:** Laravel 13 (v13.20.0, PHP 8.4)
- **Authentication:** Laravel Fortify v1.37.2 (Backend Auth) & Livewire v4.3.3 (Class-Based Components) & Flux UI v2.15.0
- **Styling:** Tailwind CSS v4.3.3
- **Testing:** Pest PHP v4.7.5 (PHPUnit 12.5.30)
- **Type Safety & Linting:** Larastan v3.10.0 (PHPStan 0 errors), Laravel Pint v1.29.3
- **Database:** MySQL (Production) / SQLite (Local testing)
- **Queue System:** Database Driver (`QUEUE_CONNECTION=database`)
- **Mail:** Laravel Mailable (`EventTicketMail`, `EventReminderMail`, `EventUpdatedMail`, `EventCancelledMail`, `OrganizerApplicationApprovedMail`, `OrganizerApplicationRejectedMail`) via SMTP
- **User Impersonation:** `lab404/laravel-impersonate` v1.7
- **AI Tooling & Monitoring:** Laravel Boost MCP Server v2.4.13, Laravel MCP v0.9.0, Laravel Pail v1.2.7

---

## 3. Core Domain Models & Relationships

### Users (`users`)
- **Roles:**
  - **Admin** (`is_admin = true`): System superuser. Bypasses all Gate/Policy checks via `Gate::before()`, reviews organizer applications, impersonates users, and manages system settings. Cannot be impersonated.
  - **Organizer** (`is_organizer = true`): Can publish, edit, and cancel events, view attendee rosters, export CSV reports, and view real-time organizer analytics.
  - **Attendee**: Browses public events, claims digital tickets, views ticket passes, and applies for organizer privileges.
- **Attributes:** `id`, `name`, `email`, `password`, `is_organizer`, `organizer_expires_at` (datetime, nullable), `is_admin`, `timestamps`.
- **Typed Relations:** `@return HasMany<Event, $this>` (`organizedEvents`), `@return HasMany<Ticket, $this>` (`tickets`), `@return HasMany<OrganizerApplication, $this>` (`organizerApplications`), `@return HasOne<OrganizerApplication, $this>` (`latestOrganizerApplication`).

### Organizer Applications (`organizer_applications`)
- **Belongs to:** User (`user()`, `@return BelongsTo<User, $this>`)
- **Attributes:** `id`, `user_id`, `organization_name`, `business_reg_number`, `contact_phone`, `bank_name`, `bank_account_number`, `event_title`, `event_description`, `proposed_starts_at`, `requested_duration_days`, `status` (`pending`, `approved`, `rejected`), `admin_notes`, `approved_until`, `timestamps`.

### Events (`events`)
- **Belongs to:** User (Organizer/Admin) (`organizer()`, `@return BelongsTo<User, $this>`)
- **Has many:** Tickets (`tickets()`, `@return HasMany<Ticket, $this>`)
- **Soft Deletes:** `SoftDeletes` trait (`deleted_at`).
- **Attributes:** `id`, `user_id`, `title`, `description`, `starts_at`, `location`, `capacity`, `tickets_sold`, `created_at`, `updated_at`, `deleted_at`.

### Tickets (`tickets`)
- **Belongs to:** User (`user()`, `@return BelongsTo<User, $this>`), Event (`event()`, `@return BelongsTo<Event, $this>`)
- **Attributes:** `id`, `event_id`, `user_id`, `ticket_code` (UUID string), `reminder_sent` (boolean), `timestamps`.

---

## 4. Key Functional Features & Architecture

### A. Event Management (Organizers & Admins)
- Create, edit, publish, and soft-delete public events.
- Capacity enforcement with real-time remaining ticket counts.
- Attendee roster viewing with real-time search and streaming CSV export (`GlobalAttendees` at `/organizer/attendees`).

### B. Organizer Analytics Dashboard
- Component: `App\Livewire\Organizer\OrganizerAnalytics` (`<livewire:organizer.organizer-analytics />`) embedded directly in `resources/views/dashboard.blade.php`.
- **Top Row (Stat Overview Cards)**: Real-time calculation of total tickets sold (with period growth % badge and sparkline SVG), registered participants joined, and published events.
- **Bottom Row (Interactive Analytics Charts)**: ApexCharts integration via Alpine.js featuring:
  - **Ticket Sales Trend**: Monthly ticket sales curve (Jan - Dec) with gradient fill.
  - **Participants Per Month**: Monthly participant registration distribution column chart.

### C. Registration & Digital Ticketing (Attendees)
- Public event discovery with search and date filters (`upcoming`, `this_week`, `this_month`, `past`).
- One-click ticket registration.
- **Concurrency Prevention:** `DB::transaction()` with pessimistic database locking (`lockForUpdate()`) on the `Event` record to guarantee capacity is never exceeded.
- Dispatch queued email (`EventTicketMail`) containing digital ticket credentials upon ticket creation (dispatched after database transaction commit).

### D. Organizer Request & Approval Workflow
- Attendees apply to become an organizer via `/event-operation/become-organizer` (`route('become-organizer.edit')`) or via Module 2 "Apply Organizer".
- Applicants fill in business entity credentials (Company Name, Registration No., Phone, Bank Payout info) and proposed event details.
- Admins review pending applications via `/admin/applications` (`App\Livewire\Admin\ReviewApplications`) and grant permanent organizer access upon approval.
- `User::isOrganizer()` returns `$user->is_organizer` as the single source of truth used across middleware (`EnsureUserIsOrganizer`) and policies (`EventPolicy`).

### E. User Impersonation & User Management
- Housed under `App\Livewire\Admin\Users` at `/admin/users` (`route('admin.users.index')`).
- Admins impersonate non-admin users via `lab404/laravel-impersonate` routes (`GET /impersonate/take/{id}`).
- Persistent top warning banner (`partials/impersonation-banner.blade.php`) displays active impersonation state and exit option (`GET /impersonate/leave`).
- Admin Gate superpower: `Gate::before()` grants admins blanket approval over all policy checks.

### F. System Health & Master Audit Tools (Admins)
- **Event Audit** (`App\Livewire\Admin\SystemEvents` at `/admin/events`): Platform-wide audit table of all active, past, and soft-deleted events with restore/cancel capabilities.
- **System Status** (`App\Livewire\Admin\SystemStatus` at `/admin/status`): Infrastructure monitoring for database, queue worker counts, and automated reminder statistics.

### G. Navigation & Sidebar Architecture
- **Module 1: Main** (`Dashboard`, `Browse Events`, `My Tickets`).
- **Module 2: Event Operations** (`Managed Events`, `Attendee Roster`, `Apply Organizer` for Attendees, `Review Requests` for Admins).
- **Module 3: System Admin** (`User Management` at `/admin/users`, `Event Audit` at `/admin/events`, `System Status` at `/admin/status` — guarded strictly for Admins and hidden during active impersonation).
- **Settings**: Single item under Bottom Desktop User Profile Menu (`route('profile.edit')`).

### H. Automated Reminders
- Custom Artisan command (`app:send-event-reminders`) scheduled via `routes/console.php`.
- Evaluates upcoming events starting within 24 hours where `reminder_sent` is `false`.
- Queues reminder emails (`EventReminderMail`) to registered attendees and sets `reminder_sent = true`.

---

## 5. Business Rules & Technical Constraints
1. **Capacity Guarantee:** Ticket registrations must use pessimistic database locking (`lockForUpdate()`) inside transactions to handle concurrent purchases safely.
2. **Asynchronous Execution:** All mail notifications implement `ShouldQueue` and execute outside uncommitted transaction boundaries.
3. **Route & Role Security:** Event creation/management routes require authenticated organizer or admin access via `EnsureUserIsOrganizer` middleware and policies.
4. **Impersonation Loop Safety:** Admins can impersonate non-admins (`canImpersonate() = true`), but admins can NEVER be impersonated (`canBeImpersonated() = false`).
5. **Type Safety & Larastan Compliance:** 100% PHPStan type safety (0 errors) enforced across models, controllers, Livewire components, mailable backoff methods (`@return array<int, int>`), and factories using explicit relation generics (`@return HasMany<..., $this>`, `@return BelongsTo<..., $this>`, `@return HasOne<..., $this>`).
6. **Testing Standards:** All feature and unit tests are written using Pest PHP (`test()` / `it()` syntax).
7. **Component Structure:** Use standard Class-based Livewire components (`app/Livewire/*` + `resources/views/livewire/*`) and Flux UI components.